(function() {
  'use strict';

  function validType(type) {
    return type === 'protanopia' || type === 'deuteranopia' || type === 'tritanopia';
  }

  function readLocal(key) {
    try { return window.localStorage.getItem(key); } catch (e) { return null; }
  }
  function writeLocal(key, val) {
    try { window.localStorage.setItem(key, val); } catch (e) { /* ignore */ }
  }
  function removeLocal(key) {
    try { window.localStorage.removeItem(key); } catch (e) { /* ignore */ }
  }

  document.addEventListener('DOMContentLoaded', function() {
    try {
      var cfg = document.getElementById('mdal-config');
      if (!cfg) { return; }
      if (cfg.dataset.pluginEnabled !== '1') { return; }

      var texts = {
        toggleOn: cfg.dataset.langToggleOn || 'On',
        toggleOff: cfg.dataset.langToggleOff || 'Off',
        modeLabel: cfg.dataset.langModeLabel || 'Mode',
        protanopia: cfg.dataset.langProtanopia || 'Protanopia',
        deuteranopia: cfg.dataset.langDeuteranopia || 'Deuteranopia',
        tritanopia: cfg.dataset.langTritanopia || 'Tritanopia',
        indicatorActive: cfg.dataset.langIndicatorActive || 'Color Blind Mode',
        indicatorOff: cfg.dataset.langIndicatorOff || 'Off',
        helpBrief: cfg.dataset.langHelpBrief || ''
      };

      var body = document.body;
      var lsKeyEnabled = 'mdal_enabled';
      var lsKeyType = 'mdal_type';
      var lsKeyVersion = 'mdal_version';

      function ensureUI() {
        var container = document.getElementById('mdal-ui');
        if (container) { return container; }
        container = document.createElement('div');
        container.id = 'mdal-ui';

        var indicator = document.createElement('div');
        indicator.id = 'mdal-indicator';
        indicator.setAttribute('role', 'status');
        indicator.setAttribute('aria-live', 'polite');
        container.appendChild(indicator);

        var panel = document.createElement('div');
        panel.id = 'mdal-panel';
        var title = document.createElement('div');
        title.className = 'mdal-panel-title';
        title.textContent = texts.modeLabel;
        panel.appendChild(title);

        var options = [
          { value: 'protanopia', label: texts.protanopia },
          { value: 'deuteranopia', label: texts.deuteranopia },
          { value: 'tritanopia', label: texts.tritanopia }
        ];
        options.forEach(function(opt) {
          var row = document.createElement('div');
          row.className = 'mdal-option';
          var input = document.createElement('input');
          input.type = 'radio';
          input.name = 'mdal-mode';
          input.value = opt.value;
          input.id = 'mdal-opt-' + opt.value;
          var label = document.createElement('label');
          label.setAttribute('for', input.id);
          label.textContent = opt.label;
          row.appendChild(input);
          row.appendChild(label);
          panel.appendChild(row);
        });
        container.appendChild(panel);

        var toggle = document.createElement('button');
        toggle.id = 'mdal-toggle';
        toggle.type = 'button';
        toggle.setAttribute('role', 'button');
        toggle.setAttribute('aria-pressed', 'false');
        toggle.setAttribute('aria-label', texts.helpBrief || texts.indicatorActive);
        container.appendChild(toggle);

        document.body.appendChild(container);
        return container;
      }

      function syncPanelSelection(type) {
        var radios = document.querySelectorAll('#mdal-panel input[name="mdal-mode"]');
        for (var i = 0; i < radios.length; i++) {
          radios[i].checked = (radios[i].value === type);
        }
      }

      function setIndicator(text) {
        var el = document.getElementById('mdal-indicator');
        if (el) { el.textContent = text; }
      }

      function applyClasses(enabled, type) {
        body.classList.toggle('mdal-on', !!enabled);
        body.classList.toggle('mdal-safe-area', !!enabled);
        body.classList.remove('mdal-protanopia', 'mdal-deuteranopia', 'mdal-tritanopia');
        if (enabled && validType(type)) {
          body.classList.add('mdal-' + type);
        }
      }

      function updateUI(enabled, type) {
        var toggle = document.getElementById('mdal-toggle');
        var panel = document.getElementById('mdal-panel');
        if (!toggle || !panel) { return; }
        toggle.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        toggle.textContent = enabled ? texts.toggleOff : texts.toggleOn;
        if (enabled) {
          var label = validType(type) ? (' ' + typeLabel(type)) : '';
          setIndicator(texts.indicatorActive + label);
        } else {
          setIndicator(texts.indicatorActive + ': ' + texts.indicatorOff);
          panel.classList.remove('mdal-panel--open');
        }
        syncPanelSelection(type || '');
      }

      function typeLabel(type) {
        if (type === 'protanopia') return texts.protanopia;
        if (type === 'deuteranopia') return texts.deuteranopia;
        if (type === 'tritanopia') return texts.tritanopia;
        return '';
      }

      function openPanel() {
        var panel = document.getElementById('mdal-panel');
        if (panel) { panel.classList.add('mdal-panel--open'); }
      }
      function closePanel() {
        var panel = document.getElementById('mdal-panel');
        if (panel) { panel.classList.remove('mdal-panel--open'); }
      }

      function setEnabled(enabled) {
        writeLocal(lsKeyEnabled, enabled ? 'true' : 'false');
      }
      function setType(type) {
        if (validType(type)) {
          writeLocal(lsKeyType, type);
        }
      }

      ensureUI();

      var initialEnabled = readLocal(lsKeyEnabled) === 'true';
      var initialType = readLocal(lsKeyType);
      if (!validType(initialType)) { initialType = null; }
      if (!readLocal(lsKeyVersion)) { writeLocal(lsKeyVersion, '1'); }

      applyClasses(initialEnabled, initialType);
      updateUI(initialEnabled, initialType);

      var toggleBtn = document.getElementById('mdal-toggle');
      toggleBtn.addEventListener('click', function() {
        var enabled = readLocal(lsKeyEnabled) === 'true';
        var type = readLocal(lsKeyType);
        if (!validType(type)) { type = null; }
        var next = !enabled;
        setEnabled(next);
        if (next) {
          if (!type) {
            openPanel();
          }
        } else {
          closePanel();
        }
        applyClasses(next, type);
        updateUI(next, type);
      });

      document.getElementById('mdal-panel').addEventListener('change', function(ev) {
        var t = ev.target;
        if (t && t.name === 'mdal-mode' && validType(t.value)) {
          setType(t.value);
          var enabled = readLocal(lsKeyEnabled) === 'true';
          applyClasses(enabled, t.value);
          updateUI(enabled, t.value);
        }
      });

      // Cierra el panel al hacer clic fuera
      document.addEventListener('click', function(ev) {
        var panel = document.getElementById('mdal-panel');
        var ui = document.getElementById('mdal-ui');
        if (!panel || !ui) return;
        if (!ui.contains(ev.target)) {
          closePanel();
        }
      });
    } catch (e) {
      // Fallback silencioso
    }
  });
})();