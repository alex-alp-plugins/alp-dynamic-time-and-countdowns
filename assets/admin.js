(function () {
  'use strict';
  var root = document.querySelector('.alpdt-admin');
  if (!root) return;
  var i18n = window.alpdtAdmin || {};
  // External SEO/security notices sometimes target the first wp-admin h1 and
  // are injected into our hero after render. Move only those notices into a
  // visible region ABOVE the hero; never hide WordPress or third-party alerts.
  (function keepHeroFreeOfNotices() {
    var hero = root.querySelector('.alpdt-hero');
    var destination = root.querySelector('#alpdt-external-notices');
    if (!hero || !destination) return;
    var selector = '.notice, .updated, .error, .update-nag, .rank-math-notice, .rank-math-notification, [role="alert"], [role="status"]';
    function relocateNotices() {
      hero.querySelectorAll(selector).forEach(function (notice) {
        if (!hero.contains(notice)) return;
        destination.hidden = false;
        destination.appendChild(notice);
      });
    }
    relocateNotices();
    if (typeof MutationObserver === 'function') {
      new MutationObserver(relocateNotices).observe(hero, {childList:true, subtree:true});
    }
  })();
  // ALP accessible dropdown: keep original selects as the source of truth and as no-JS fallbacks.
  // Do not style native Divi controls. Scoped to this administration page only.
  function makeALPDropdown(select) {
    if (!select || select.dataset.alpdtEnhanced) return;
    var container = document.createElement('div');
    container.className = 'alpdt-picker';
    var trigger = document.createElement('button');
    trigger.type = 'button'; trigger.className = 'alpdt-picker-trigger';
    trigger.setAttribute('aria-haspopup', 'listbox'); trigger.setAttribute('aria-expanded', 'false');
    var list = document.createElement('div');
    list.className = 'alpdt-picker-options'; list.setAttribute('role', 'listbox'); list.hidden = true;
    var listId = 'alpdt-picker-' + select.id;
    trigger.id = 'alpdt-picker-trigger-' + select.id;
    list.id = listId; trigger.setAttribute('aria-controls', listId); list.setAttribute('aria-labelledby', trigger.id);
    var label = root.querySelector('label[for="' + select.id + '"]');
    trigger.setAttribute('aria-label', label ? label.textContent.trim() : (select.getAttribute('aria-label') || (i18n.selectOption || 'Choose an option')));
    if (label) label.htmlFor = trigger.id;
    select.parentNode.insertBefore(container, select.nextSibling);
    container.appendChild(trigger); container.appendChild(list);
    select.classList.add('alpdt-picker-native'); select.tabIndex = -1;
    select.setAttribute('aria-hidden', 'true'); select.dataset.alpdtEnhanced = '1';
    var entries = [], activeIndex = 0;
    function repaint() {
      entries.forEach(function (entry, index) {
        var active = select.selectedIndex === index;
        entry.setAttribute('aria-selected', active ? 'true' : 'false');
        entry.classList.toggle('alpdt-picker-selected', active);
      });
      var selected = select.options[select.selectedIndex];
      trigger.textContent = selected ? selected.textContent : '';
      var caret = document.createElement('span'); caret.className = 'alpdt-picker-chevron'; caret.setAttribute('aria-hidden', 'true'); trigger.appendChild(caret);
    }
    function close(focus) {
      list.hidden = true; trigger.setAttribute('aria-expanded', 'false'); container.classList.remove('alpdt-picker-open');
      if (focus) trigger.focus();
    }
    function open() {
      root.querySelectorAll('.alpdt-picker-open').forEach(function (openPicker) {
        if (openPicker !== container && openPicker.alpdtClose) openPicker.alpdtClose(false);
      });
      list.hidden = false; trigger.setAttribute('aria-expanded', 'true'); container.classList.add('alpdt-picker-open');
      var room = window.innerHeight - trigger.getBoundingClientRect().bottom;
      list.classList.toggle('alpdt-picker-above', room < 240 && trigger.getBoundingClientRect().top > room);
      activeIndex = Math.max(0, select.selectedIndex);
      if (entries[activeIndex]) entries[activeIndex].focus();
    }
    container.alpdtClose = close;
    Array.prototype.forEach.call(select.options, function (option, i) {
      var entry = document.createElement('button'); entry.type = 'button'; entry.className = 'alpdt-picker-option';
      entry.setAttribute('role', 'option'); entry.tabIndex = -1; entry.textContent = option.textContent;
      entry.disabled = option.disabled;
      list.appendChild(entry); entries.push(entry);
      entry.addEventListener('click', function () { choose(i); });
      entry.addEventListener('keydown', function (event) {
        var n = i;
        if (event.key === 'ArrowDown') n = Math.min(entries.length - 1, i + 1);
        else if (event.key === 'ArrowUp') n = Math.max(0, i - 1);
        else if (event.key === 'Home') n = 0;
        else if (event.key === 'End') n = entries.length - 1;
        else if (event.key === 'Escape') { event.preventDefault(); close(true); return; }
        else if (event.key === 'Tab') { close(false); return; }
        else if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); choose(i); return; }
        else if (event.key.length === 1 && /[\p{L}\p{N}]/u.test(event.key)) {
          var lower = event.key.toLocaleLowerCase();
          var match = entries.findIndex(function (e, k) { return k !== i && e.textContent.toLocaleLowerCase().indexOf(lower) === 0; });
          if (match >= 0) n = match; else return;
        } else return;
        event.preventDefault(); if (entries[n] && !entries[n].disabled) entries[n].focus();
      });
    });
    function choose(index) {
      if (!select.options[index] || select.options[index].disabled) return;
      var old = select.value; select.selectedIndex = index; repaint(); close(true);
      if (old !== select.value) select.dispatchEvent(new Event('change', { bubbles: true }));
    }
    trigger.addEventListener('click', function () { if (list.hidden) open(); else close(true); });
    trigger.addEventListener('keydown', function (event) {
      if (['ArrowDown','ArrowUp','Enter',' '].indexOf(event.key) > -1) { event.preventDefault(); open(); }
    });
    select.addEventListener('change', repaint);
    select.alpdtRepaint = function () {
      entries.forEach(function (entry, i) { if (select.options[i]) entry.textContent = select.options[i].textContent; }); repaint();
    };
    repaint();
  }
  Array.prototype.forEach.call(root.querySelectorAll('.alpdt-field select'), makeALPDropdown);
  document.addEventListener('pointerdown', function (event) {
    root.querySelectorAll('.alpdt-picker-open').forEach(function (picker) {
      if (!picker.contains(event.target) && picker.alpdtClose) picker.alpdtClose(false);
    });
  });

  var tabs = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-tab]'));
  function activate(name, focus) {
    tabs.forEach(function (tab) {
      var active = tab.dataset.alpdtTab === name;
      tab.setAttribute('aria-selected', active ? 'true' : 'false');
      tab.tabIndex = active ? 0 : -1;
      var panel = document.getElementById('alpdt-panel-' + tab.dataset.alpdtTab);
      if (panel) panel.hidden = !active;
      if (active && focus) tab.focus();
    });
  }
  tabs.forEach(function (tab, index) {
    tab.addEventListener('click', function () { activate(tab.dataset.alpdtTab, false); });
    tab.addEventListener('keydown', function (ev) {
      if (['ArrowRight', 'ArrowLeft', 'Home', 'End'].indexOf(ev.key) === -1) return;
      ev.preventDefault();
      var next = ev.key === 'Home' ? 0 : ev.key === 'End' ? tabs.length - 1 : (index + (ev.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length;
      activate(tabs[next].dataset.alpdtTab, true);
    });
  });
  var guideTabs = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-guide-tab]'));
  function activateGuideTab(name, focus) {
    guideTabs.forEach(function(tab) {
      var active = tab.dataset.alpdtGuideTab === name;
      tab.setAttribute('aria-selected', active ? 'true' : 'false');
      tab.tabIndex = active ? 0 : -1;
      var panel = document.getElementById('alpdt-guide-panel-' + tab.dataset.alpdtGuideTab);
      if (panel) panel.hidden = !active;
      if (active && focus) tab.focus();
    });
  }
  guideTabs.forEach(function(tab,index){
    tab.addEventListener('click',function(){activateGuideTab(tab.dataset.alpdtGuideTab,false);});
    tab.addEventListener('keydown',function(e){
      if(['ArrowLeft','ArrowRight','Home','End'].indexOf(e.key) === -1) return;
      e.preventDefault();
      var next = e.key === 'Home' ? 0 : e.key === 'End' ? guideTabs.length - 1 : (index + (e.key === 'ArrowRight' ? 1 : -1) + guideTabs.length) % guideTabs.length;
      activateGuideTab(guideTabs[next].dataset.alpdtGuideTab,true);
    });
  });
  if (window.location.hash === '#alpdt-panel-compat') activate('compat', false);
  if (new URLSearchParams(window.location.search).has('alpdt_message')) activate('dates', false);
  if (new URLSearchParams(window.location.search).get('alpdt_view') === 'shortcodes') activate('shortcodes', false);

  var type = document.getElementById('alpdt-type'), postPreviewDate = document.getElementById('alpdt-preview-post-date'), exprHelp = document.getElementById('alpdt-expression-help'), from = document.getElementById('alpdt-from'),
    to = document.getElementById('alpdt-to'), saved = document.getElementById('alpdt-saved'),
    mode = document.getElementById('alpdt-mode'), unit = document.getElementById('alpdt-unit'),
    txt = document.getElementById('alpdt-text'), offset = document.getElementById('alpdt-offset'),
    format = document.getElementById('alpdt-format'), countdownDisplay = document.getElementById('alpdt-countdown-display'),
    copyrightStyle = document.getElementById('alpdt-copyright-style'), clockStyle = document.getElementById('alpdt-clock-style'), clockLayout = document.getElementById('alpdt-clock-layout'), lightsMode = document.getElementById('alpdt-lights-mode'), celebrationMessage = document.getElementById('alpdt-celebration-message'), celebrationDuration = document.getElementById('alpdt-celebration-duration'), celebrationMode = document.getElementById('alpdt-celebration-mode'), celebrationPreview = document.getElementById('alpdt-celebration-preview'), preview = document.getElementById('alpdt-preview'),
    code = document.getElementById('alpdt-shortcode'), copy = document.getElementById('alpdt-copy'),
    seoArea = document.getElementById('alpdt-seo-actions'), seoProvider = document.getElementById('alpdt-seo-provider'),
    seoCopy = document.getElementById('alpdt-seo-copy'), seoValue = document.getElementById('alpdt-seo-value'),
    seoHelp = document.getElementById('alpdt-seo-help'), seoStatus = document.getElementById('alpdt-seo-status'),
    msgBefore = document.getElementById('alpdt-message-before'), msgOn = document.getElementById('alpdt-message-on'), msgAfter = document.getElementById('alpdt-message-after'),
    previewCaption = document.getElementById('alpdt-preview-caption'),
    status = document.getElementById('alpdt-status'), typeHelp = document.getElementById('alpdt-type-help'),
    fromLabel = document.querySelector('label[for="alpdt-from"]'), fromHelp = document.getElementById('alpdt-from-help'),
    enterSaved = saved && saved.options && saved.options[0],
    unitChecks = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-clock-unit]')),
    labelInputs = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-unit-label]')),
    designInputs = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-design]')),
    panelTransparent = document.getElementById('alpdt-panel-transparent'), panelColor = document.getElementById('alpdt-panel-color');
  // Per-group and per-axis link state is editor-only. Only numeric values are stored.
  var paddingSides = ['top','right','bottom','left'];
  var paddingLinks = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-padding-link]'));
  var paddingPrefixes = {card:'clock_padding_',countdown:'countdown_padding_'};
  var paddingInputs = {};
  Object.keys(paddingPrefixes).forEach(function(scope){
    paddingInputs[scope] = {};
    paddingSides.forEach(function(side){
      paddingInputs[scope][side] = root.querySelector('[data-alpdt-design="' + paddingPrefixes[scope] + side + '"]');
    });
  });
  function setPaddingLinked(button, active) {
    button.setAttribute('aria-pressed', active ? 'true' : 'false');
    button.classList.toggle('alpdt-linked', active);
  }
  function resetPaddingLinks(){ paddingLinks.forEach(function(button){setPaddingLinked(button,false);}); }
  function linkedSides(axis){return axis==='vertical'?['top','bottom']:['left','right'];}
  paddingLinks.forEach(function(button){
    button.addEventListener('click',function(){
      var parts=button.dataset.alpdtPaddingLink.split(':');
      var inputs=paddingInputs[parts[0]];
      if(!inputs)return;
      var active=button.getAttribute('aria-pressed')!=='true';
      setPaddingLinked(button,active);
      if(active){
        var sides=linkedSides(parts[1]),first=inputs[sides[0]],second=inputs[sides[1]];
        var value=first && first.value!==''?first.value:(second?second.value:'');
        sides.forEach(function(side){if(inputs[side])inputs[side].value=value;});
      }
      refresh();
    });
  });
  Object.keys(paddingInputs).forEach(function(scope){
    paddingSides.forEach(function(side){
      var input=paddingInputs[scope][side];if(!input)return;
      ['input','change'].forEach(function(eventName){input.addEventListener(eventName,function(){
        var axis=(side==='top'||side==='bottom')?'vertical':'horizontal';
        var button=root.querySelector('[data-alpdt-padding-link="'+scope+':'+axis+'"]');
        if(button && button.getAttribute('aria-pressed')==='true'){
          linkedSides(axis).forEach(function(target){if(paddingInputs[scope][target])paddingInputs[scope][target].value=input.value;});
        }
      });});
    });
  });

  // Optional palette slots are actual stored colors, not cosmetic editor chips.
  var extraColorCounts={confetti:4,balloon:4,christmas:5};
  function syncExtraColorFields(){
    var style=clockStyle ? clockStyle.value : '';
    root.querySelectorAll('[data-alpdt-extra-color-field]').forEach(function(field){
      var meta=field.dataset.alpdtExtraColorField.split(':'),kind=meta[0],index=Number(meta[1]);
      field.hidden=index>extraColorCounts[kind] || (kind==='christmas' && style!=='christmas_lights');
    });
    root.querySelectorAll('[data-alpdt-add-color]').forEach(function(button){
      var kind=button.dataset.alpdtAddColor,max=Number(button.dataset.alpdtMax);
      button.disabled=extraColorCounts[kind]>=max;
    });
  }
  root.querySelectorAll('[data-alpdt-add-color]').forEach(function(button){
    button.addEventListener('click',function(){
      var kind=button.dataset.alpdtAddColor,max=Number(button.dataset.alpdtMax);
      extraColorCounts[kind]=Math.min(max,extraColorCounts[kind]+1);
      syncExtraColorFields();
      var field=root.querySelector('[data-alpdt-extra-color-field="'+kind+':'+extraColorCounts[kind]+'"]');
      if(field){var first=field.querySelector('input');if(first){first.dataset.alpdtTouched='1';first.focus();}}
      refresh();
    });
  });
  // One stored attribute per font size; unit selectors are presentation controls.
  // Clamp accepts exactly three safe CSS lengths, never arbitrary CSS/functions.
  var fontUnitControls = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-font-unit]'));
  function validLengthPart(text, insideClamp, key) {
    var match = /^([0-9]{1,4}(?:\.[0-9]{1,3})?)(px|rem|em|%|vw|vh)$/.exec(text.trim().toLowerCase());
    if(!match)return false;
    var n=Number(match[1]), unit=match[2];
    var ceiling=unit==='px'?(insideClamp?300:(key==='digit_size'?110:200)):(unit==='em'||unit==='rem'?25:(unit==='%'?1200:100));
    return n >= (insideClamp?0:(unit==='px'?(key==='digit_size'?18:6):.01)) && n <= ceiling;
  }
  function fontValue(input) {
    var key=input.dataset.alpdtDesign;
    var select=root.querySelector('[data-alpdt-font-unit="'+key+'"]');
    var unit=select?select.value:'px', value=input.value.trim().toLowerCase();
    if(unit==='clamp') {
      var match=/^clamp\(\s*([^,()]+)\s*,\s*([^,()]+)\s*,\s*([^,()]+)\s*\)$/.exec(value);
      return match && match.slice(1).every(function(part){return validLengthPart(part,true,key);}) ?
        'clamp('+match.slice(1).map(function(part){return part.trim();}).join(', ')+')' : '';
    }
    if(!/^[0-9]{1,4}(?:\.[0-9]{1,3})?$/.test(value))return '';
    var combined=value+unit;
    return validLengthPart(combined,false,key)?(unit==='px'?value:combined):'';
  }
  function fontSet(input, value) {
    var select=root.querySelector('[data-alpdt-font-unit="'+input.dataset.alpdtDesign+'"]');
    var match=/^([0-9]{1,4}(?:\.[0-9]{1,3})?)(px|rem|em|%|vw|vh)$/.exec(String(value).trim().toLowerCase());
    if(/^clamp\(/i.test(String(value).trim())) {
      select.value='clamp';input.value=String(value).trim();
    } else if(match) {select.value=match[2];input.value=match[1];}
    else {select.value='px';input.value=value;}
    input.placeholder=select.value==='clamp'?'clamp(20px, 4vw, 72px)':'';
    input.inputMode=select.value==='clamp'?'text':'decimal';
    input.closest('.alpdt-font-size-field').classList.toggle('alpdt-clamp-size',select.value==='clamp');
    if(select.alpdtRepaint)select.alpdtRepaint();
    input.classList.remove('alpdt-invalid');input.removeAttribute('aria-invalid');input.setCustomValidity('');
    select.dataset.alpdtPreviousUnit=select.value;
  }
  fontUnitControls.forEach(function(select) {
    var input=root.querySelector('[data-alpdt-design="'+select.dataset.alpdtFontUnit+'"]');
    if(!input)return;
    select.addEventListener('change',function() {
      var old=select.dataset.alpdtPreviousUnit||'px';
      var number=Number(input.value), valid=Number.isFinite(number);
      if(select.value==='clamp') {
        if(!/^clamp\(/.test(input.value)){
          var px=old==='px'&&valid?number:(input.dataset.alpdtDesign==='digit_size'?60:14);
          input.value='clamp('+Math.max(1,Math.round(px*.4))+'px, '+(input.dataset.alpdtDesign==='digit_size'?'4vw':'2vw')+', '+Math.min(300,Math.max(6,Math.round(px)))+'px)';
        }
      } else if(old==='clamp'){
        input.value=input.dataset.alpdtDesign==='digit_size'?'60':'14';
      } else if(valid && old!==select.value) {
        // Approximate conversions use a 16px root and a 1440x900 reference viewport.
        var px=old==='rem'||old==='em'?number*16:old==='%'?number*16/100:old==='vw'?number*14.4:old==='vh'?number*9:number;
        var converted=select.value==='rem'||select.value==='em'?px/16:select.value==='%'?px*100/16:select.value==='vw'?px/14.4:select.value==='vh'?px/9:px;
        input.value=String(Math.round(converted*1000)/1000);
      }
      select.dataset.alpdtPreviousUnit=select.value;
      input.placeholder=select.value==='clamp'?'clamp(20px, 4vw, 72px)':'';
      input.inputMode=select.value==='clamp'?'text':'decimal';
      input.closest('.alpdt-font-size-field').classList.toggle('alpdt-clamp-size',select.value==='clamp');
      if(select.alpdtRepaint)select.alpdtRepaint();
      input.dispatchEvent(new Event('input',{bubbles:true}));
      input.dispatchEvent(new Event('change',{bubbles:true}));
    });
    select.dataset.alpdtPreviousUnit='px';
    input.addEventListener('input',function() {
      var valid=!!fontValue(input);
      input.classList.toggle('alpdt-invalid',!valid);
      input.setCustomValidity(valid?'':(i18n.fontSizeInvalid||'Enter a valid font size for the selected unit.'));
      if(valid)input.removeAttribute('aria-invalid');else input.setAttribute('aria-invalid','true');
    });
  });
  function invalidFontInput() {
    var invalid=designInputs.find(function(input) {
      return /^(digit_size|label_size)$/.test(input.dataset.alpdtDesign) && !fontValue(input);
    });
    if(invalid) {invalid.setAttribute('aria-invalid','true');invalid.classList.add('alpdt-invalid');}
    return invalid;
  }

  // Date expressions, recurring countdowns and end actions share the ALP form controls.
  var expression=document.getElementById('alpdt-expression'),hemisphere=document.getElementById('alpdt-hemisphere'),
    recurrence=document.getElementById('alpdt-recurrence'),repeatTime=document.getElementById('alpdt-repeat-time'),
    repeatWeekday=document.getElementById('alpdt-repeat-weekday'),repeatDay=document.getElementById('alpdt-repeat-day'),
    endAction=document.getElementById('alpdt-end-action'),endMessage=document.getElementById('alpdt-end-message'),endUrl=document.getElementById('alpdt-end-url');
  var diffFrom=document.getElementById('alpdt-difference-from'),diffTo=document.getElementById('alpdt-difference-to'),live=document.getElementById('alpdt-live');
  // Scoped nested tabs. Native inputs remain intact and keep their current values when switching tabs.
  var clockTabs = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-clock-tab]'));
  var clockTabName = 'units';
  function activateClockTab(name, focus) {
    clockTabName = name;
    clockTabs.forEach(function(tab) {
      var selected = tab.dataset.alpdtClockTab === name;
      tab.setAttribute('aria-selected', selected ? 'true' : 'false');
      tab.tabIndex = selected ? 0 : -1;
      var panel = document.getElementById('alpdt-clock-panel-' + tab.dataset.alpdtClockTab);
      if (panel) panel.hidden = !selected;
      if (selected && focus) tab.focus();
    });
  }
  clockTabs.forEach(function(tab, index) {
    tab.addEventListener('click', function(){activateClockTab(tab.dataset.alpdtClockTab,false);});
    tab.addEventListener('keydown', function(event) {
      var keys=['ArrowRight','ArrowLeft','Home','End']; if(keys.indexOf(event.key)===-1)return;
      event.preventDefault();
      var visible=clockTabs.filter(function(item){return !item.hidden;});
      var current=visible.indexOf(tab), indexTo=event.key==='Home'?0:event.key==='End'?visible.length-1:(current+(event.key==='ArrowRight'?1:-1)+visible.length)%visible.length;
      if(visible[indexTo])activateClockTab(visible[indexTo].dataset.alpdtClockTab,true);
    });
  });
  function syncClockTabs() {
    var custom = countdownDisplay.value === 'custom';
    var unitsTab = document.getElementById('alpdt-clock-tab-units');
    if (unitsTab) unitsTab.hidden = !custom;
    if (!custom && clockTabName === 'units') activateClockTab('style',false);
  }
  if (countdownDisplay) countdownDisplay.addEventListener('change',function(){
    activateClockTab(countdownDisplay.value === 'custom' ? 'units' : 'style',false);
  });
  if (panelTransparent && panelColor) {
    panelTransparent.addEventListener('change',function(){
      panelColor.disabled = panelTransparent.checked;
      var swatch = root.querySelector('[data-alpdt-color-picker="panel_color"]');
      if (swatch) { swatch.disabled = panelTransparent.checked; if (panelTransparent.checked && swatch.alpdtClose) swatch.alpdtClose(false); }
      refresh();
    });
  }
  // Standalone Divi-inspired ALP palette editor. No Divi internals or network assets.
  // 8-digit HEX is CSS Color 4: RRGGBBAA; six-digit saved colors remain unchanged.
  var isHex = function (v) { return /^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/.test(v); };
  var openColor = null;
  var quickDefaults = ['#000000', '#F33B49', '#053197', '#07B57B', '#FFCC00', '#FFFFFF'];
  var recentColors = Array.isArray(i18n.quickColorValues) ? i18n.quickColorValues.filter(function(c){return /^#[0-9a-fA-F]{6}(?:[0-9a-fA-F]{2})?$/.test(c);}).map(function(c){return c.toUpperCase();}).slice(0,24) : quickDefaults.slice();
  var paletteRenderers = [], paletteStatus = [], paletteSaving = false;
  function announcePalette(message,error) {
    paletteStatus.forEach(function(item){item.textContent=message;item.classList.toggle('alpdt-palette-error',!!error);});
  }
  function redrawPalettes() { paletteRenderers.forEach(function(render){render();}); }
  function savePalette(colors) {
    if (paletteSaving) return Promise.resolve(false);
    if (!i18n.quickColorsUrl || !i18n.quickColorsNonce) {
      announcePalette(i18n.quickColorFailed||'Could not save Quick Colors.',true);
      return Promise.resolve(false);
    }
    paletteSaving=true;
    var body = new URLSearchParams({action:'alpdt_save_quick_colors',nonce:i18n.quickColorsNonce,colors:JSON.stringify(colors)});
    return fetch(i18n.quickColorsUrl,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:body.toString()})
      .then(function(res){return res.json().then(function(data){if(!res.ok || !data.success || !data.data || !Array.isArray(data.data.colors))throw Error((data.data && data.data.message)||i18n.quickColorFailed);return data.data.colors;});})
      .then(function(next){paletteSaving=false;recentColors=next;redrawPalettes();return true;})
      .catch(function(err){paletteSaving=false;announcePalette(err.message||i18n.quickColorFailed,true);return false;});
  }
  var quickDeleteDialog = null, quickDeleteOrigin = null, quickDeleteValue = null;
  function requestQuickColorRemoval(value,button) {
    if (paletteSaving) return;
    quickDeleteValue=value;quickDeleteOrigin=button;
    if (!quickDeleteDialog) {
      quickDeleteDialog=elem('dialog','alpdt-dialog alpdt-quick-delete-dialog',root);
      var ico=elem('span','alpdt-dialog-warning',quickDeleteDialog,'!');ico.setAttribute('aria-hidden','true');
      var ttl=elem('h2','',quickDeleteDialog,i18n.removeQuickColorTitle||'Remove this quick color?');ttl.id='alpdt-quick-delete-title';quickDeleteDialog.setAttribute('aria-labelledby',ttl.id);
      var desc=elem('p','',quickDeleteDialog,i18n.removeQuickColorHelp||'This will remove the color from your palette.');desc.id='alpdt-quick-delete-description';quickDeleteDialog.setAttribute('aria-describedby',desc.id);
      var footer=elem('div','alpdt-dialog-actions',quickDeleteDialog);
      var cancel=elem('button','button',footer,i18n.cancel||'Cancel');cancel.type='button';
      var confirm=elem('button','button alpdt-confirm-danger',footer,i18n.removeQuickColorConfirm||'Remove color');confirm.type='button';
      cancel.addEventListener('click',function(){quickDeleteDialog.close();});
      confirm.addEventListener('click',function(){
        if (!quickDeleteValue || paletteSaving) return;
        confirm.disabled=true;
        savePalette(recentColors.filter(function(c){return c!==quickDeleteValue;})).then(function(ok){
          confirm.disabled=false;
          if(ok){announcePalette(i18n.quickColorRemoved||'Color removed.');quickDeleteDialog.close();}
        });
      });
      quickDeleteDialog.addEventListener('close',function(){
        if(quickDeleteDialog.open)return; // An earlier close event must not clear a newly opened confirmation.
        if(quickDeleteOrigin && quickDeleteOrigin.isConnected)quickDeleteOrigin.focus();
        else if(document.querySelector('.alpdt-color-pop:not([hidden]) .alpdt-color-add'))document.querySelector('.alpdt-color-pop:not([hidden]) .alpdt-color-add').focus();
        quickDeleteOrigin=null;quickDeleteValue=null;
      });
    }
    if (typeof quickDeleteDialog.showModal === 'function')quickDeleteDialog.showModal();
    else {announcePalette(i18n.quickColorFailed||'This browser cannot show confirmations.',true);return;}
    quickDeleteDialog.querySelector('.alpdt-dialog-actions .button').focus();
  }
  function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }
  function fromHSV(h, s, v) {
    var c=v*s, x=c*(1-Math.abs((h/60)%2-1)), m=v-c, rgb;
    if(h<60)rgb=[c,x,0];else if(h<120)rgb=[x,c,0];else if(h<180)rgb=[0,c,x];
    else if(h<240)rgb=[0,x,c];else if(h<300)rgb=[x,0,c];else rgb=[c,0,x];
    return '#'+rgb.map(function(n){return Math.round((n+m)*255).toString(16).padStart(2,'0');}).join('').toUpperCase();
  }
  function toHSV(v) {
    var hex=v.slice(0,7), nums=[1,3,5].map(function(i){return parseInt(hex.slice(i,i+2),16)/255;});
    var r=nums[0],g=nums[1],b=nums[2],max=Math.max(r,g,b),min=Math.min(r,g,b),delta=max-min,h=0;
    if(delta){if(max===r)h=60*(((g-b)/delta)%6);else if(max===g)h=60*((b-r)/delta+2);else h=60*((r-g)/delta+4);}
    return {h:(h+360)%360,s:max===0?0:delta/max,v:max};
  }
  function getOpacity(v){return v.length===9?Math.round(parseInt(v.slice(7),16)*100/255):100;}
  function withAlpha(rgb,percent){return percent>=100?rgb:rgb+Math.round(clamp(percent,0,100)*255/100).toString(16).padStart(2,'0').toUpperCase();}
  function elem(tag,klass,parent,text) {
    var e=document.createElement(tag);if(klass)e.className=klass;if(text!=null)e.textContent=text;if(parent)parent.appendChild(e);return e;
  }
  Array.prototype.forEach.call(root.querySelectorAll('[data-alpdt-color-picker]'),function(swatch){
    var name=swatch.dataset.alpdtColorPicker;
    var hex=designInputs.find(function(input){return input.dataset.alpdtDesign===name;});
    if(!hex)return;
    var defaultPickerColor=isHex(hex.value)?hex.value:(isHex(swatch.dataset.alpdtColor)?swatch.dataset.alpdtColor:'#FFFFFF');
    hex.dataset.alpdtValid=isHex(hex.value)?hex.value:'';
    var shell=swatch.closest('.alpdt-color-controls');
    var pop=elem('section','alpdt-color-pop',shell);pop.hidden=true;
    var popId='alpdt-color-pop-'+name;pop.id=popId;
    pop.setAttribute('role','dialog');pop.setAttribute('aria-modal','false');
    var header=elem('div','alpdt-color-pop-head',pop);
    var heading=elem('h4','alpdt-color-pop-title',header,i18n.colorPicker||'Color Picker');heading.id=popId+'-title';
    pop.setAttribute('aria-labelledby',heading.id);
    var close=elem('button','alpdt-color-close',header,'×');close.type='button';close.setAttribute('aria-label',i18n.closeColor||'Close color picker');
    var body=elem('div','alpdt-color-pop-body',pop);
    var square=elem('div','alpdt-color-square',body);square.tabIndex=0;
    square.setAttribute('role','group');square.setAttribute('aria-label',i18n.saturationBrightness||'Saturation and brightness');
    var marker=elem('span','alpdt-color-marker',square);marker.setAttribute('aria-hidden','true');
    var sliders=elem('div','alpdt-color-slider-row',body);
    var sample=elem('div','alpdt-color-sample',sliders);sample.setAttribute('aria-hidden','true');
    var samplePaint=elem('span','alpdt-color-sample-paint',sample);
    var tracks=elem('div','alpdt-color-tracks',sliders);
    var hueLabel=elem('label','alpdt-color-range-label',tracks,i18n.hue||'Hue');var hue=elem('input','alpdt-color-hue',hueLabel);
    hue.type='range';hue.min='0';hue.max='359';hue.step='1';hue.setAttribute('aria-label',i18n.hue||'Hue');
    var alphaLabel=elem('label','alpdt-color-range-label',tracks,i18n.opacity||'Opacity');var alpha=elem('input','alpdt-color-alpha',alphaLabel);
    alpha.type='range';alpha.min='0';alpha.max='100';alpha.step='1';alpha.setAttribute('aria-label',i18n.opacity||'Opacity');
    var values=elem('div','alpdt-color-value-row',body);
    var editGroup=elem('div','alpdt-color-edit-group',values);
    var label=elem('label','alpdt-color-pop-label',editGroup,i18n.hexValue||'HEX color');label.htmlFor=popId+'-hex';
    var edit=elem('input','alpdt-color-pop-hex',editGroup);edit.id=popId+'-hex';edit.type='text';edit.maxLength=9;edit.spellcheck=false;edit.autocomplete='off';
    var opacityGroup=elem('div','alpdt-color-opacity-group',values);
    var percentLabel=elem('label','alpdt-color-pop-label',opacityGroup,i18n.opacity||'Opacity');percentLabel.htmlFor=popId+'-opacity';
    var percent=elem('input','alpdt-color-percent',opacityGroup);percent.type='number';percent.id=popId+'-opacity';percent.min='0';percent.max='100';percent.step='1';
    var sign=elem('span','alpdt-color-percent-sign',opacityGroup,'%');sign.setAttribute('aria-hidden','true');
    var quick=elem('div','alpdt-color-quick',pop);
    var quickHead=elem('div','alpdt-quick-head',quick);
    var quickTitle=elem('h5','alpdt-color-quick-title',quickHead,i18n.quickColors||'Quick colors');
    var quickActions=elem('div','alpdt-quick-actions',quickHead);
    var quickManage=elem('button','alpdt-quick-manage',quickActions,i18n.manageColors||'Manage colors');quickManage.type='button';quickManage.setAttribute('aria-pressed','false');
    var quickAdd=elem('button','alpdt-quick-add',quickActions,i18n.addColor||'Add color');quickAdd.type='button';
    quickAdd.setAttribute('aria-label',i18n.addQuickColor||'Add current color to Quick Colors');
    var managingColors=false;
    quickManage.addEventListener('click',function(){managingColors=!managingColors;quickManage.setAttribute('aria-pressed',managingColors?'true':'false');quickManage.textContent=managingColors?(i18n.doneManaging||'Done'):(i18n.manageColors||'Manage colors');renderQuickColors();});
    var chips=elem('div','alpdt-color-chips',quick);
    var quickFeedback=elem('p','alpdt-color-quick-feedback',quick);quickFeedback.setAttribute('role','status');quickFeedback.setAttribute('aria-live','polite');paletteStatus.push(quickFeedback);
    var hsv=toHSV(defaultPickerColor), opacity=getOpacity(defaultPickerColor);
    function paint(){
      var rgb=fromHSV(hsv.h,hsv.s,hsv.v);
      square.style.setProperty('--alpdt-hue',hsv.h);
      marker.style.left=(hsv.s*100)+'%';marker.style.top=((1-hsv.v)*100)+'%';
      hue.value=String(Math.round(hsv.h));alpha.value=String(opacity);percent.value=String(opacity);
      chips.querySelectorAll('.alpdt-color-chip').forEach(function(b){b.setAttribute('aria-pressed',b.dataset.alpdtChipValue===hex.value.toUpperCase()?'true':'false');});
      var previewColor=isHex(hex.value)?hex.value:defaultPickerColor;
      samplePaint.style.backgroundColor=previewColor;
      swatch.style.setProperty('--alpdt-swatch',previewColor);
      swatch.dataset.alpdtColor=previewColor;
      edit.value=previewColor.toUpperCase();
      alpha.style.setProperty('--alpdt-alpha-base',rgb);
    }
    function setColor(value){
      if(!isHex(value))return;
      hex.dataset.alpdtTouched='1';hex.value=value.toUpperCase();hex.dataset.alpdtValid=hex.value;
      hex.classList.remove('alpdt-invalid');hex.removeAttribute('aria-invalid');
      hsv=toHSV(hex.value);opacity=getOpacity(hex.value);paint();
      hex.dispatchEvent(new Event('input',{bubbles:true}));
    }
    function setHSV(){setColor(withAlpha(fromHSV(hsv.h,hsv.s,hsv.v),opacity));}
    function place(){
      // Keep the palette in the *visible wp-admin content viewport*, not in
      // the column's coordinate space. The old 'flip above' calculation could
      // place an oversized palette behind the WordPress toolbar/left admin menu.
      var anchor=shell.getBoundingClientRect();
      var content=document.getElementById('wpcontent');
      var bounds=content?content.getBoundingClientRect():null;
      var leftLimit=Math.max(12,bounds?bounds.left+12:12);
      var rightLimit=Math.min(window.innerWidth-12,bounds?bounds.right-12:window.innerWidth-12);
      if(rightLimit<=leftLimit+80){leftLimit=12;rightLimit=window.innerWidth-12;}
      var toolbar=document.getElementById('wpadminbar');
      var toolbarBottom=toolbar?toolbar.getBoundingClientRect().bottom:0;
      var topLimit=Math.max(12,toolbarBottom+12);
      var bottomLimit=window.innerHeight-12;
      var width=Math.max(1,Math.min(368,rightLimit-leftLimit));
      pop.style.width=width+'px';
      pop.style.maxHeight='none';
      // The picker is position:fixed, so both axes use viewport coordinates.
      pop.style.right='auto';pop.style.bottom='auto';
      var height=Math.min(pop.scrollHeight,Math.max(1,bottomLimit-topLimit));
      var x=clamp(anchor.right-width,leftLimit,rightLimit-width);
      var below=bottomLimit-anchor.bottom-8;
      var above=anchor.top-topLimit-8;
      var y;
      if(below>=height || below>=above){y=anchor.bottom+8;}
      else{y=anchor.top-height-8;}
      y=clamp(y,topLimit,Math.max(topLimit,bottomLimit-height));
      pop.style.left=x+'px';pop.style.top=y+'px';
      pop.style.maxHeight=Math.max(1,bottomLimit-y)+'px';
    }
    function closePicker(restore){
      pop.hidden=true;swatch.setAttribute('aria-expanded','false');hex.setAttribute('aria-expanded','false');shell.classList.remove('alpdt-color-active');
      if(openColor===closePicker)openColor=null;if(restore)swatch.focus();
    }
    function openPicker(){
      if(openColor && openColor!==closePicker)openColor(false);
      pop.hidden=false;swatch.setAttribute('aria-expanded','true');hex.setAttribute('aria-expanded','true');shell.classList.add('alpdt-color-active');openColor=closePicker;
      var safe=isHex(hex.value)?hex.value:(isHex(hex.dataset.alpdtValid)?hex.dataset.alpdtValid:defaultPickerColor);hsv=toHSV(safe);opacity=getOpacity(safe);paint();
      place();pop.scrollTop=0;edit.focus({preventScroll:true});edit.select();
    }
    swatch.setAttribute('aria-haspopup','dialog');swatch.setAttribute('aria-controls',popId);
    // Clicking a HEX value (including #000000) opens the same accessible picker.
    // Direct typing still works from the popup's HEX field or the original input.
    hex.setAttribute('aria-haspopup','dialog');hex.setAttribute('aria-controls',popId);
    hex.setAttribute('aria-expanded','false');
    hex.addEventListener('click',function(){if(pop.hidden)openPicker();});
    hex.addEventListener('keydown',function(event){
      if(event.key==='F4'||(event.altKey&&event.key==='ArrowDown')){
        event.preventDefault();openPicker();
      }
    });
    swatch.addEventListener('click',function(){if(pop.hidden)openPicker();else closePicker(true);});
    swatch.alpdtClose=closePicker;
    close.addEventListener('click',function(){closePicker(true);});
    pop.addEventListener('keydown',function(event){if(event.key==='Escape'){event.preventDefault();event.stopPropagation();closePicker(true);}});
    hue.addEventListener('input',function(){hsv.h=Number(hue.value);setHSV();});
    alpha.addEventListener('input',function(){opacity=Number(alpha.value);setHSV();});
    percent.addEventListener('change',function(){opacity=clamp(Number(percent.value)||0,0,100);setHSV();});
    function moveSquare(event){
      var rect=square.getBoundingClientRect();
      hsv.s=clamp((event.clientX-rect.left)/rect.width,0,1);
      hsv.v=1-clamp((event.clientY-rect.top)/rect.height,0,1);setHSV();
    }
    square.addEventListener('pointerdown',function(event){event.preventDefault();square.setPointerCapture(event.pointerId);moveSquare(event);square.focus();});
    square.addEventListener('pointermove',function(event){if(square.hasPointerCapture(event.pointerId))moveSquare(event);});
    square.addEventListener('keydown',function(event){
      var step=event.shiftKey?.1:.02;
      if(event.key==='ArrowLeft')hsv.s=clamp(hsv.s-step,0,1);
      else if(event.key==='ArrowRight')hsv.s=clamp(hsv.s+step,0,1);
      else if(event.key==='ArrowUp')hsv.v=clamp(hsv.v+step,0,1);
      else if(event.key==='ArrowDown')hsv.v=clamp(hsv.v-step,0,1);
      else return;
      event.preventDefault();setHSV();
    });
    edit.addEventListener('input',function(){edit.classList.toggle('alpdt-invalid',!isHex(edit.value));if(isHex(edit.value))setColor(edit.value);});
    edit.addEventListener('blur',function(){if(!isHex(edit.value)){edit.value=hex.dataset.alpdtValid||'#FFFFFF';edit.classList.remove('alpdt-invalid');}});
    hex.addEventListener('input',function(){
      if(hex.dataset.alpdtApplyingTheme!=='1')hex.dataset.alpdtTouched='1';
      var valid=isHex(hex.value);hex.classList.toggle('alpdt-invalid',!valid);
      if(valid){hex.removeAttribute('aria-invalid');hex.dataset.alpdtValid=hex.value.toUpperCase();hsv=toHSV(hex.value);opacity=getOpacity(hex.value);paint();}
      else hex.setAttribute('aria-invalid','true');
    });
    hex.addEventListener('blur',function(){
      if(name==='glow_background_color' && hex.value.trim()===''){
        // An empty optional color must stay unset after focus/blur.
        hex.dataset.alpdtValid='';hex.classList.remove('alpdt-invalid');hex.removeAttribute('aria-invalid');
        hex.dispatchEvent(new Event('change',{bubbles:true}));return;
      }
      if(!isHex(hex.value)){hex.value=hex.dataset.alpdtValid||defaultPickerColor;hex.classList.remove('alpdt-invalid');hex.removeAttribute('aria-invalid');}
      hex.value=hex.value.toUpperCase();paint();hex.dispatchEvent(new Event('change',{bubbles:true}));
    });
    swatch.alpdtSetColor=function(value){if(!isHex(value))return;hex.value=value.toUpperCase();hex.dataset.alpdtValid=hex.value;hsv=toHSV(value);opacity=getOpacity(value);paint();};
    function renderQuickColors() {
      chips.replaceChildren();
      chips.classList.toggle('alpdt-is-managing',managingColors);
      if(!recentColors.length)elem('p','alpdt-color-empty',chips,i18n.quickColorEmpty||'No quick colors saved. Use + to add one.');
      recentColors.forEach(function(value){
        var tile=elem('div','alpdt-color-tile',chips);
        var button=elem('button','alpdt-color-chip',tile);button.type='button';button.style.setProperty('--alpdt-chip',value);button.dataset.alpdtChipValue=value;button.setAttribute('aria-pressed',value===hex.value.toUpperCase()?'true':'false');
        button.setAttribute('aria-label',value);button.title=value;
        button.addEventListener('click',function(){setColor(value);});
        var remove=elem('button','alpdt-color-chip-remove',tile,'×');remove.type='button';
        remove.setAttribute('aria-label',(i18n.removeQuickColor||'Remove color %s from Quick Colors').replace('%s',value));
        remove.setAttribute('title',(i18n.removeQuickColor||'Remove color %s from Quick Colors').replace('%s',value));
        remove.hidden=!managingColors;
        remove.addEventListener('click',function(){requestQuickColorRemoval(value,remove);});
      });
      quickAdd.disabled=recentColors.length>=24;
    }
    quickAdd.addEventListener('click',function(){
        if(paletteSaving)return;
        var color=hex.value.toUpperCase();
        if(!isHex(color)){announcePalette(i18n.quickColorFailed||'Choose a valid HEX color.',true);return;}
        if(recentColors.includes(color)){announcePalette(i18n.quickColorAlreadySaved||'This color is already saved.',false);return;}
        if(recentColors.length>=24){announcePalette(i18n.quickColorLimit||'You can save up to 24 colors.',true);return;}
        savePalette(recentColors.concat([color])).then(function(ok){if(ok)announcePalette(i18n.quickColorAdded||'Color added.');});
      });
    paletteRenderers.push(renderQuickColors);
    renderQuickColors();
    paint();
  });
  document.addEventListener('pointerdown',function(event){
    if(event.target.closest('.alpdt-quick-delete-dialog'))return;
    if(openColor && !event.target.closest('.alpdt-color-controls'))openColor(false);
  });
  window.addEventListener('resize',function(){if(openColor)openColor(false);});
  document.addEventListener('scroll',function(event){
    if(openColor && !(event.target instanceof Element && event.target.closest('.alpdt-color-pop')))openColor(false);
  },true);
  // Flip has a dark reference-inspired preset, without overwriting user-picked colors.
  var colorPalettes = {
    flip: {panel_color:'#0F0F0F',digit_color:'#F8F8F8',label_color:'#B5B5B5',card_color:'#232323',accent_color:'#FF5D16',track_color:'#E1E8F0'},
    standard: {panel_color:'#0F0F0F',digit_color:'#DE4848',label_color:'#243665',card_color:'#FFFFFF',accent_color:'#2FC5B5',track_color:'#E1E8F0'},
    snowfall: {digit_color:'#FFFFFF',label_color:'#D2FAFF',card_color:'#003075',accent_color:'#1DEEDA',track_color:'#B8EDFF'},
    christmas_lights: {digit_color:'#FFFFFF',label_color:'#1DEEDA',card_color:'#003075',accent_color:'#F9D76E',track_color:'#FFFFFF',christmas_1_color:'#FF4958',christmas_2_color:'#21ED8E',christmas_3_color:'#FFDB52',christmas_4_color:'#63B5FF',christmas_5_color:'#ED71F9'},
    minimalist: {digit_color:'#FFFFFF',label_color:'#FFDB4D',card_color:'#18183C',accent_color:'#FFFFFF',track_color:'#FFFFFF'},
    odometer: {digit_color:'#F8F8F8',label_color:'#243665',card_color:'#222D42',accent_color:'#FFCC00'},
    neon: {digit_color:'#49F2EF',label_color:'#243665',card_color:'#10192C',accent_color:'#FF4FD6'},
    liquid: {digit_color:'#FFFFFF',label_color:'#243665',card_color:'#102B3C',accent_color:'#23C9BF'},
    matrix: {digit_color:'#07B57B',label_color:'#243665',card_color:'#101C19',accent_color:'#07B57B'},
    orbit: {digit_color:'#EAFFFF',label_color:'#243665',card_color:'#0C1E3C',accent_color:'#07B57B'},
    glitch: {digit_color:'#FFFFFF',label_color:'#243665',card_color:'#14151C',accent_color:'#F33B49'},
    morph: {digit_color:'#FFCC00',label_color:'#243665',card_color:'#13223C',accent_color:'#FFCC00'},
    hourglass: {digit_color:'#FFFFFF',label_color:'#243665',card_color:'#142038',accent_color:'#FFCC00'},
    confetti: {panel_color:'#10182A',digit_color:'#FFFFFF',label_color:'#E4E9F7',card_color:'#182544',accent_color:'#FFCC00',track_color:'#E1E8F0'},
    blood: {digit_color:'#C81D32',label_color:'#702020',card_color:'#FFF1DB',accent_color:'#8D0E22',track_color:'#3D141F'}
  };
  if (clockStyle) clockStyle.addEventListener('change', function () {
    var palette = colorPalettes[clockStyle.value] || colorPalettes.standard;
    designInputs.forEach(function (hex) {
      var next = palette[hex.dataset.alpdtDesign];
      if (!next || hex.dataset.alpdtTouched === '1') return;
      hex.dataset.alpdtApplyingTheme = '1';
      hex.value = next;
      hex.dataset.alpdtValid = next;
      var picker = root.querySelector('[data-alpdt-color-picker="'+hex.dataset.alpdtDesign+'"]');
      if (picker && picker.alpdtSetColor) picker.alpdtSetColor(next);
      hex.dispatchEvent(new Event('input', {bubbles:true}));
      delete hex.dataset.alpdtApplyingTheme;
    });
  });
  var resetAppearance=document.getElementById('alpdt-reset-appearance');
  if (resetAppearance) resetAppearance.addEventListener('click',function(){
    extraColorCounts={confetti:4,balloon:4,christmas:5};syncExtraColorFields();
    var palette=colorPalettes[clockStyle.value]||colorPalettes.standard;
    designInputs.forEach(function(input){
      var key=input.dataset.alpdtDesign;
      if(['clock_weight','clock_italic','card_inner_spacing','bulb_card_gap','bulb_count','glow_background_color'].includes(key) || key==='clock_radius' || key==='clock_padding' || /^(?:clock|countdown)_padding_(?:top|right|bottom|left)$/.test(key)){input.value='';delete input.dataset.alpdtTouched;input.dispatchEvent(new Event('change',{bubbles:true}));return;}
      var defaults={clock_size:'170',digit_size:'60',label_size:'14',clock_font:'auto',blood_font:'creepster',clock_width:'auto'};
      var value=palette[key]||defaults[key]||(/^(?:confetti|balloon)_(?:[1-9]|10)_color$/.test(key)||/^christmas_(?:[1-9]|1[0-2])_color$/.test(key)?input.defaultValue:'');if(!value)return;
      delete input.dataset.alpdtTouched;
      input.value=value;input.dataset.alpdtValid=value;
      if(key==='digit_size'||key==='label_size')fontSet(input,value);
      var picker=root.querySelector('[data-alpdt-color-picker="'+key+'"]');
      if(picker&&picker.alpdtSetColor)picker.alpdtSetColor(value);
      input.classList.remove('alpdt-invalid');input.removeAttribute('aria-invalid');
      input.dispatchEvent(new Event('change',{bubbles:true}));
    });
    resetPaddingLinks();
    if (panelTransparent) { panelTransparent.checked=false; panelTransparent.dispatchEvent(new Event('change',{bubbles:true})); }
    if(openColor)openColor(false);
  });
  var defaultFromLabel = fromLabel && fromLabel.textContent, defaultFromHelp = fromHelp && fromHelp.textContent;
  var defaultSavedText = enterSaved && enterSaved.textContent;
  var fields = Array.prototype.slice.call(root.querySelectorAll('[data-alpdt-field]'));
  var requestId = 0, debounce;
  var usesStart = { elapsed: true, ordinal: true, copyright: true, anniversary_message: true };
  function updateFields() {
    var t = type.value, selectedSaved = saved && saved.value !== '';
    preview.setAttribute('aria-live', t === 'countdown' && ['full','custom'].includes(countdownDisplay.value) ? 'off' : 'polite');
    var isCurrentCopyright = t === 'copyright' && copyrightStyle && copyrightStyle.value === 'current';
    var active = {
      saved: !!usesStart[t] && !!saved && !isCurrentCopyright,
      from: !!usesStart[t] && !selectedSaved && !isCurrentCopyright,
      to: t === 'countdown' && recurrence.value === 'none',
      date_difference: t === 'smart_date' && expression.value === 'date_difference',
      post_preview_date: t === 'smart_date' && ['post_published','post_modified'].includes(expression.value),
      live: t === 'smart_date' && !['post_published','post_modified'].includes(expression.value),
      smart_expr: t === 'smart_date',
      hemisphere: t === 'smart_date' && expression.value === 'season',
      recurrence: t === 'countdown',
      repeat_time: t === 'countdown' && recurrence.value !== 'none',
      repeat_weekday: t === 'countdown' && recurrence.value === 'weekly',
      repeat_day: t === 'countdown' && recurrence.value === 'monthly',
      end_action: t === 'countdown' && recurrence.value === 'none',
      end_message: t === 'countdown' && recurrence.value === 'none' && endAction.value === 'message',
      end_url: t === 'countdown' && recurrence.value === 'none' && endAction.value === 'redirect',
      countdown_display: t === 'countdown',
      clock_tabs: t === 'countdown' && ['full','custom'].includes(countdownDisplay.value),
      flip_panel_background: t === 'countdown' && ['full','custom'].includes(countdownDisplay.value) && ['flip','celebration','confetti'].includes(clockStyle.value),
      clock_style: t === 'countdown' && ['full','custom'].includes(countdownDisplay.value),
      lights_mode: false,
      clock_layout: t === 'countdown' && ['full','custom'].includes(countdownDisplay.value) && ['simple','glow','flip','slide','pulse','sweep','ring','odometer','neon','liquid','matrix','glitch','morph','celebration','confetti','snowfall','christmas_lights'].includes(clockStyle.value),
      celebration: t === 'countdown' && ['full','custom'].includes(countdownDisplay.value) && ['celebration','confetti'].includes(clockStyle.value),
      clock_units: t === 'countdown' && countdownDisplay.value === 'custom',
      clock_design: t === 'countdown' && ['full','custom'].includes(countdownDisplay.value),
      clock_labels: t === 'countdown' && ['full','custom'].includes(countdownDisplay.value),
      anniversary_messages: t === 'anniversary_message',
      copyright_style: t === 'copyright',
      mode: t === 'elapsed' || t === 'ordinal',
      unit: t === 'elapsed',
      text: t === 'elapsed',
      offset: t === 'year',
      format: t === 'date' || (t === 'smart_date' && !['quarter','season','date_difference'].includes(expression.value))
    };
    fields.forEach(function (field) { field.hidden = !active[field.dataset.alpdtField]; });
    root.querySelectorAll('[data-alpdt-confetti-palette]').forEach(function(field){field.hidden=clockStyle.value!=='confetti';});
    var style = clockStyle.value;
    // Display only controls whose CSS variables affect the selected appearance.
    var appearanceControls = {
      clock_size:['ring','liquid','orbit'],
      bulb_count:['christmas_lights'],
      glow_background_color:['glow'],
      bulb_card_gap:['christmas_lights'],
      clock_weight:['simple','glow','flip','slide','pulse','sweep','ring','celebration','confetti','odometer','neon','liquid','orbit','glitch','hourglass','snowfall','christmas_lights'],
      clock_italic:['simple','glow','flip','slide','pulse','sweep','ring','celebration','confetti','odometer','neon','liquid','orbit','glitch','hourglass','snowfall','christmas_lights'],
      clock_radius:['flip','odometer','neon','matrix','morph','glitch','hourglass','celebration','confetti','blood','snowfall','christmas_lights'],
      card_color:['flip','ring','odometer','neon','liquid','matrix','morph','orbit','glitch','hourglass','snowfall','christmas_lights'],
      accent_color:['flip','sweep','ring','celebration','neon','liquid','matrix','morph','orbit','glitch','hourglass'],
      track_color:['ring','orbit','hourglass'],
      christmas_1_color:['christmas_lights'],
      christmas_2_color:['christmas_lights'],
      christmas_3_color:['christmas_lights'],
      christmas_4_color:['christmas_lights'],
      christmas_5_color:['christmas_lights']
    };
    root.querySelectorAll('[data-alpdt-appearance-only]').forEach(function(field){
      var key=field.dataset.alpdtAppearanceOnly;
      // Winter themes have only three standard colors that actually affect CSS:
      // numbers, unit labels, and the entire seasonal background. Christmas
      // Lights additionally exposes five repeating bulb colors.
      var winter = style === 'snowfall' || style === 'christmas_lights';
      if (winter) {
        field.hidden = !['digit_color','label_color','card_color','clock_radius','clock_weight','clock_italic'].includes(key) &&
          !(style === 'christmas_lights' && (key==='bulb_card_gap' || key==='bulb_count' || /^christmas_[1-5]_color$/.test(key)));
      } else {
        field.hidden = !!appearanceControls[key] && !appearanceControls[key].includes(style);
      }
    });
    syncExtraColorFields();
    var cardLabel=root.querySelector('label[for="alpdt-card-color"]');
    if(cardLabel)cardLabel.textContent=style==='snowfall'?(i18n.snowfallBackground||'Snowfall background'):style==='christmas_lights'?(i18n.christmasBackground||'Christmas Lights background'):(i18n.cardBackground||'Card / circle background');
    var sizeSection=root.querySelector('[data-alpdt-design-section="size"]');
    if(sizeSection)sizeSection.hidden=!['ring','liquid','orbit'].includes(style);
    var diameterLabel=root.querySelector('label[for="alpdt-clock-size"]');
    if(diameterLabel) diameterLabel.textContent=style==='orbit'?i18n.orbitDiameter||'Orbit diameter (px)':style==='liquid'?i18n.liquidDiameter||'Liquid circle diameter (px)':i18n.circleDiameter||'Circle diameter (px)';
    root.querySelectorAll('[data-alpdt-blood-font]').forEach(function(field){field.hidden=style!=='blood';});
    var fontApplicable = !['matrix','morph','blood'].includes(clockStyle.value);
    root.querySelectorAll('.alpdt-font-field,.alpdt-font-help').forEach(function(el){el.hidden=!fontApplicable;});
    root.querySelectorAll('[data-alpdt-design-section]').forEach(function(section){
      var fields=section.querySelectorAll('.alpdt-design-field');
      section.hidden=!!fields.length && !Array.prototype.some.call(fields,function(field){return !field.hidden;});
    });
    syncClockTabs();
    if (typeHelp) typeHelp.textContent = (i18n.helpTypes && i18n.helpTypes[t]) || '';
    if (exprHelp && t === 'smart_date') exprHelp.textContent = ['post_published','post_modified'].includes(expression.value)
      ? (i18n.previewPostDateNeeded || 'Choose a sample date for the preview. The published page uses its actual post date.')
      : (i18n.smartDateHelp || 'Calendar expressions calculate automatically. For a difference between dates, enter two dates below.');
    if (fromLabel) fromLabel.textContent = t === 'copyright' ? i18n.copyrightFromLabel : defaultFromLabel;
    if (fromHelp) fromHelp.textContent = t === 'copyright' ? i18n.copyrightFromHelp : defaultFromHelp;
    if (enterSaved) { enterSaved.textContent = t === 'copyright' ? i18n.enterYear : defaultSavedText; if (saved.alpdtRepaint) saved.alpdtRepaint(); }
  }
  function paletteChanged(kind) {
    // Existing saved shortcodes without palette attributes preserve the seven-color classic palette.
    return extraColorCounts[kind]>4 || designInputs.some(function(input){return new RegExp('^'+kind+'_(?:[1-9]|10)_color$').test(input.dataset.alpdtDesign) && input.dataset.alpdtTouched==='1';});
  }
  function attrs() {
    var t = type.value, data = { type: t };
    if (usesStart[t] && !(t === 'copyright' && copyrightStyle.value === 'current')) {
      if (saved && saved.value) data.id = saved.value;
      else data.from = from.value.trim();
    }
    if (t === 'smart_date') { if(live.checked && !['post_published','post_modified'].includes(expression.value))data.live='true';data.expression=expression.value; if (expression.value==='date_difference') { data.from=diffFrom.value;data.to=diffTo.value; } if (expression.value==='season')data.hemisphere=hemisphere.value; if (!['quarter','season','date_difference'].includes(expression.value))data.format=format.value; }
    if (t === 'countdown') { if (recurrence.value !== 'none') { data.recurrence=recurrence.value;data.repeat_time=repeatTime.value; if(recurrence.value==='weekly')data.repeat_weekday=repeatWeekday.value; if(recurrence.value==='monthly')data.repeat_day=repeatDay.value; } else { data.to=to.value; if(endAction.value!=='none'){data.end_action=endAction.value;if(endAction.value==='message')data.end_message=endMessage.value.trim();if(endAction.value==='redirect')data.end_url=endUrl.value.trim();}} data.countdown_display = countdownDisplay.value; if (['full','custom'].includes(countdownDisplay.value) && clockStyle.value !== 'simple') data.clock_style = clockStyle.value; if (['full','custom'].includes(countdownDisplay.value) && clockLayout && clockLayout.value === 'horizontal' && ['simple','glow','flip','slide','pulse','sweep','ring','odometer','neon','liquid','matrix','glitch','morph','celebration','confetti','snowfall','christmas_lights'].includes(clockStyle.value)) data.clock_layout='horizontal'; if (countdownDisplay.value === 'custom') data.clock_units = unitChecks.filter(function(el){return el.checked;}).map(function(el){return el.dataset.alpdtClockUnit;}).join(','); }
    if (t === 'countdown' && ['full','custom'].includes(countdownDisplay.value) && clockStyle.value==='christmas_lights' && lightsMode && lightsMode.value) data.lights_mode=lightsMode.value;
    if (t === 'countdown' && ['full','custom'].includes(countdownDisplay.value) && ['celebration','confetti'].includes(clockStyle.value)) { data.celebration_duration=celebrationDuration ? celebrationDuration.value : '5'; data.celebration_mode=celebrationMode ? celebrationMode.value : 'end'; if (celebrationMessage && celebrationMessage.value.trim()) data.celebration_message=celebrationMessage.value.trim().slice(0,120); }
    if (t === 'countdown' && ['full','custom'].includes(countdownDisplay.value)) designInputs.forEach(function (input) {
      var key=input.dataset.alpdtDesign;
      var owner=input.closest('.alpdt-design-field');
      if (owner && owner.hidden) return;
      if (/^(?:clock_radius|clock_padding(?:_(?:top|right|bottom|left))?|countdown_padding_(?:top|right|bottom|left))$/.test(key) && (input.value==='' || !/^(?:[0-9]|[1-7][0-9]|80)$/.test(input.value))) return;
      if (key==='clock_width' && input.value!=='full') return;
      if (key==='card_inner_spacing' && !/^(?:[0-9]|[12][0-9]|30)$/.test(input.value)) return;
      if (key==='bulb_count' && (clockStyle.value!=='christmas_lights' || !/^[0-9]{1,2}$/.test(input.value) || Number(input.value)<8 || Number(input.value)>60)) return;
      if (key==='bulb_card_gap' && (clockStyle.value!=='christmas_lights' || !/^(?:[0-9]|[1-6][0-9]|70)$/.test(input.value))) return;
      if (key==='clock_weight' && input.value!=='' && !/^(300|400|500|600|700|800|900)$/.test(input.value)) return;
      if (key==='clock_italic' && input.value!=='' && !/^(normal|italic)$/.test(input.value)) return;
      if (['clock_weight','clock_italic'].includes(key) && input.value==='') return;
      if (key==='blood_font' && clockStyle.value!=='blood') return;
      if (key==='clock_font' && input.value==='auto') return;
      if(key==='glow_background_color' && clockStyle.value!=='glow')return;
      if (key==='digit_size'||key==='label_size') {var cssSize=fontValue(input);if(cssSize)data[key]=cssSize;return;}
      if (/^(?:confetti|balloon)_(?:[1-9]|10)_color$/.test(key)) {
        if (clockStyle.value!=='confetti' || !paletteChanged(key.split('_')[0])) return;
      }
      if (input.value !== '' && (!key.endsWith('_color') || isHex(input.value))) data[key]=input.value;
    });
    if (t === 'countdown' && ['full','custom'].includes(countdownDisplay.value) && ['flip','celebration','confetti'].includes(clockStyle.value) && panelTransparent && panelTransparent.checked) data.panel_color='transparent';
    if (t === 'countdown' && ['full','custom'].includes(countdownDisplay.value)) labelInputs.forEach(function(input){if(input.value.trim())data['label_'+input.dataset.alpdtUnitLabel]=input.value.trim().slice(0,32);});
    if (t === 'anniversary_message') { [['before',msgBefore],['on',msgOn],['after',msgAfter]].forEach(function(pair){if(pair[1] && pair[1].value.trim())data[pair[0]]=pair[1].value.trim().slice(0,240);}); }
    if (t === 'copyright') data.copyright_style = copyrightStyle.value;
    if (t === 'elapsed' || t === 'ordinal') data.mode = mode.value;
    if (t === 'elapsed') { data.unit = unit.value; data.text = txt.checked ? 'true' : 'false'; }
    if (t === 'year' && offset.value !== '0') data.offset = offset.value;
    if (t === 'date') data.format = format.value;
    return data;
  }
  function sameSavedConfiguration(original,generated) {
    var left=parseStoredConfig(original),right=parseStoredConfig(generated);
    if(!left || !right)return false;
    var defaults={mode:'anniversary',unit:'years',text:'true',format:'j F Y',offset:'0',copyright_style:'range'};
    return Array.from(new Set(Object.keys(left).concat(Object.keys(right)))).every(function(key){
      return String(left[key]===undefined?(defaults[key]===undefined?'':defaults[key]):left[key])===
        String(right[key]===undefined?(defaults[key]===undefined?'':defaults[key]):right[key]);
    });
  }
  function seoRegisteredName(data) {
    if(editingConfig && sameSavedConfiguration(editingConfig.code,code.value) && i18n.seoSavedVars && i18n.seoSavedVars[editingConfig.slug])return i18n.seoSavedVars[editingConfig.slug];
    if(data.type==='year' && (!data.offset || data.offset==='0'))return 'alpdt_year';
    if(data.type==='month')return 'alpdt_month';
    if(data.type==='copyright' && data.copyright_style==='current')return 'alpdt_year';
    if(data.type==='elapsed' && data.id && data.mode==='calendar' && data.unit==='years' && data.text==='false')return 'alpdt_since_'+data.id.replace(/-/g,'_');
    return '';
  }
  function refreshSEO(data) {
    if(!seoArea || !seoCopy)return;
    seoArea.hidden = data.type==='countdown';
    if(seoArea.hidden)return;
    var registered=seoRegisteredName(data);
    var name=registered?(seoProvider.value==='yoast'?'%%'+registered+'%%':'%'+registered+'%'):'';
    seoCopy.disabled=!name;
    seoCopy.dataset.alpdtSeoValue=name;
    seoValue.hidden=!name;
    seoValue.textContent=name;
    seoHelp.textContent=name?(i18n.seoReady||'Ready for the selected SEO plugin.'):(editingConfig?(i18n.seoUnsavedChanges||'Save your changes first.'):(i18n.seoSaveFirst||'Save this configuration first.'));
    seoStatus.replaceChildren();seoStatus.classList.remove('alpdt-copy-ok');
  }
  if(seoProvider)seoProvider.addEventListener('change',function(){refreshSEO(attrs());});
  if(seoCopy)seoCopy.addEventListener('click',function(){
    var value=seoCopy.dataset.alpdtSeoValue||'';if(!value)return;
    function failure(){
      var selection=window.getSelection(),range=document.createRange();range.selectNodeContents(seoValue);selection.removeAllRanges();selection.addRange(range);
      showCopyNotice('variable',false);
    }
    if(navigator.clipboard && navigator.clipboard.writeText)navigator.clipboard.writeText(value).then(function(){showCopyNotice('variable',true);}).catch(failure);
    else failure();
  });
  // Editing a message previews that exact field rather than today's automatic phase.
  // The phase is not added to attrs() or any saved shortcode.
  var anniversaryPreviewPhase = '';
  [[msgBefore,'before'],[msgOn,'on'],[msgAfter,'after']].forEach(function(entry) {
    if (!entry[0]) return;
    entry[0].addEventListener('focus', function() {
      anniversaryPreviewPhase = entry[1];
      refresh();
    });
    entry[0].addEventListener('input', function() { anniversaryPreviewPhase = entry[1]; });
  });
  function refresh() {
    updateFields();
    var data = attrs(), parts = ['[alpdt_time'];
    if (previewCaption) {
      var messages = { before: i18n.previewAnniversaryBefore, on: i18n.previewAnniversaryOn, after: i18n.previewAnniversaryAfter };
      previewCaption.textContent = data.type === 'anniversary_message' && anniversaryPreviewPhase ? (messages[anniversaryPreviewPhase] || '') : (i18n.previewOrdinaryCaption || 'This is how the dynamic value will appear on your site.');
    }
    Object.keys(data).forEach(function (key) {
      if (data[key] !== '') parts.push(' ' + key + '="' + String(data[key]).replace(/["\[\]<>\\]/g, '') + '"');
    });
    code.value = parts.join('') + ']';
    refreshSEO(data);
    var valid = ['elapsed', 'ordinal', 'copyright', 'anniversary_message'].indexOf(data.type) < 0 || !!(data.id || data.from) || (data.type === 'copyright' && data.copyright_style === 'current');
    if (data.type === 'countdown') valid = (data.recurrence || !!data.to) && (data.countdown_display !== 'custom' || !!data.clock_units) && (data.end_action!=='redirect' || /^\/(?!\/)[a-zA-Z0-9/_-]*$/.test(data.end_url));
    if (data.type === 'smart_date' && data.expression==='date_difference') valid = /^\d{4}-\d{2}-\d{2}$/.test(data.from) && /^\d{4}-\d{2}-\d{2}$/.test(data.to);
    ['glow','flip','slide','pulse','sweep','ring','celebration','confetti','blood','odometer','neon','liquid','matrix','orbit','glitch','morph','hourglass','snowfall','christmas_lights'].forEach(function(style) {
      preview.classList.toggle('alpdt-preview-' + style, data.type === 'countdown' && ['full','custom'].includes(data.countdown_display) && clockStyle.value === style);
    });
    stopClock();
    if (!valid) { requestId++; preview.textContent = i18n.previewEmpty || '—'; return; }
    var current = ++requestId;
    preview.textContent = '…';
    clearTimeout(debounce);
    debounce = setTimeout(function () {
      var url = new URL(i18n.restUrl, document.baseURI);
      // The server returns text and a target. The clock design is previewed locally.
      ['type', 'id', 'from', 'to', 'mode', 'unit', 'format', 'text', 'offset', 'before', 'on', 'after',
        'countdown_display', 'copyright_style', 'clock_units', 'expression', 'hemisphere', 'recurrence', 'repeat_time', 'repeat_weekday', 'repeat_day'].forEach(function (key) {
        if (data[key] !== undefined) url.searchParams.set(key, data[key]);
      });
      if (data.type === 'anniversary_message' && anniversaryPreviewPhase) {
        url.searchParams.set('preview_message', anniversaryPreviewPhase);
      }
      if (data.type === 'smart_date' && ['post_published','post_modified'].includes(data.expression) && postPreviewDate) {
        // Preview-only value, never included in attrs() or persisted to saved shortcodes.
        url.searchParams.set('preview_date', postPreviewDate.value);
      }
      fetch(url.href, { credentials: 'same-origin', cache: 'no-store' })
        .then(function (response) { if (!response.ok) throw new Error('network'); return response.json(); })
        .then(function (response) { if (current === requestId) {
          preview.textContent = response.value || i18n.previewEmpty || '—';
          if (data.type === 'countdown' && ['full','custom'].includes(data.countdown_display) && Number(response.target) > 0) startClock(Number(response.target), data.countdown_display === 'custom' ? data.clock_units : '');
        } })
        .catch(function () { if (current === requestId) preview.textContent = i18n.previewUnavailable || '—'; });
    }, 160);
  }
  [type, from, to, diffFrom, diffTo, postPreviewDate, live, saved, mode, unit, txt, offset, format, countdownDisplay, copyrightStyle, clockStyle, clockLayout, lightsMode, celebrationMessage, celebrationDuration, celebrationMode, msgBefore, msgOn, msgAfter, expression, hemisphere, recurrence, repeatTime, repeatWeekday, repeatDay, endAction, endMessage, endUrl].concat(unitChecks,designInputs,labelInputs).filter(Boolean).forEach(function (el) {
    el.addEventListener('input', refresh); el.addEventListener('change', refresh);
  });
  var clockTimer = null;
  function stopClock() { if (clockTimer !== null) { window.clearInterval(clockTimer); clockTimer = null; } }
  function applyDesign(grid) {
    grid.classList.toggle('alpdt-full-width', (root.querySelector('#alpdt-clock-width') || {}).value==='full');
    var props={ digit_color:'--alpdt-digit-color',label_color:'--alpdt-label-color',card_color:'--alpdt-card-color',accent_color:'--alpdt-accent-color',track_color:'--alpdt-track-color',panel_color:'--alpdt-flip-panel-bg',clock_size:'--alpdt-size',digit_size:'--alpdt-digit-size',label_size:'--alpdt-label-size',clock_radius:'--alpdt-border-radius',clock_weight:'--alpdt-clock-font-weight',clock_italic:'--alpdt-clock-font-style',card_inner_spacing:'--alpdt-snow-extra-width',bulb_card_gap:'--alpdt-bulb-card-gap',glow_background_color:'--alpdt-glow-bg',clock_padding_top:'--alpdt-pad-top',clock_padding_right:'--alpdt-pad-right',clock_padding_bottom:'--alpdt-pad-bottom',clock_padding_left:'--alpdt-pad-left',countdown_padding_top:'--alpdt-outer-top',countdown_padding_right:'--alpdt-outer-right',countdown_padding_bottom:'--alpdt-outer-bottom',countdown_padding_left:'--alpdt-outer-left',clock_font:'--alpdt-font',blood_font:'--alpdt-blood-font',christmas_1_color:'--alpdt-light-1-color',christmas_2_color:'--alpdt-light-2-color',christmas_3_color:'--alpdt-light-3-color',christmas_4_color:'--alpdt-light-4-color',christmas_5_color:'--alpdt-light-5-color'};
    for(var xi=6;xi<=12;xi++)props['christmas_'+xi+'_color']='--alpdt-light-'+xi+'-color';
    var fonts={auto:'',system:'system-ui, sans-serif',arial:'Arial, Helvetica, sans-serif',verdana:'Verdana, sans-serif',georgia:'Georgia, serif',trebuchet:'"Trebuchet MS", sans-serif',monospace:'ui-monospace, monospace',inter:'"ALPDT Inter", system-ui, sans-serif',poppins:'"ALPDT Poppins", sans-serif',montserrat:'"ALPDT Montserrat", sans-serif',lato:'"ALPDT Lato", Arial, sans-serif',fredoka:'"ALPDT Fredoka", sans-serif',orbitron:'"ALPDT Orbitron", sans-serif',snowburst:'"ALPDT Snowburst One", Georgia, serif',almonte_snow:'"ALPDT Almonte Snow", Georgia, serif',mountains_christmas:'"ALPDT Mountains of Christmas", Georgia, serif',creepster:'"ALPDT Creepster", "Creepster", cursive',nosifer:'"ALPDT Nosifer", "Nosifer", cursive'};
    var activeFont=(root.querySelector('#alpdt-clock-font')||{}).value||'auto';
    grid.classList.toggle('alpdt-font-almonte-snow',activeFont==='almonte_snow');
    designInputs.forEach(function(input){var key=input.dataset.alpdtDesign, value=input.value;
      if (key==='blood_font') value=value==='nosifer'?"'ALPDT Nosifer', 'Nosifer', cursive":"'ALPDT Creepster', 'Creepster', cursive";
      else if(key==='clock_width') return;
      else if(key==='clock_font') { if(value==='auto')return;value=fonts[value]||fonts.system; }
      else if(key==='clock_weight') { if (!/^(300|400|500|600|700|800|900)$/.test(value)) return; grid.classList.add('alpdt-has-typography'); }
      else if(key==='clock_italic') { if (!/^(normal|italic)$/.test(value)) return; grid.classList.add('alpdt-has-typography'); }
      else if(key==='card_inner_spacing') { if (!/^(?:[0-9]|[12][0-9]|30)$/.test(value)) return; value=Number(value)+'px'; }
      else if(key==='glow_background_color') {if(!isHex(value))return;grid.classList.add('alpdt-has-glow-background');}
      else if(key==='bulb_count') return;
      else if(key==='bulb_card_gap') { if (!/^(?:[0-9]|[1-6][0-9]|70)$/.test(value)) return; value=Number(value)+'px'; grid.classList.add('alpdt-has-bulb-gap'); }
      else if(key==='clock_radius') {
        if(value==='' || !/^(?:[0-9]|[1-7][0-9]|80)$/.test(value))return;
        value=Number(value)+'px';
        grid.classList.add('alpdt-has-radius');
      }
      else if(/^(?:clock_padding(?:_(?:top|right|bottom|left))?|countdown_padding_(?:top|right|bottom|left))$/.test(key)) {
        if(value==='' || !/^(?:[0-9]|[1-7][0-9]|80)$/.test(value)) return;
        value=Number(value)+'px';
        if(key==='clock_padding') { ['top','right','bottom','left'].forEach(function(side){grid.style.setProperty('--alpdt-pad-'+side,value);}); return; }
      } else if(key==='digit_size'||key==='label_size') {
        value=fontValue(input);
        if(!value)return;
        if(!/px$/.test(value))grid.classList.add('alpdt-has-fluid-typography');
        value=/^[0-9]+(?:\.[0-9]+)?$/.test(value)?value+'px':value;
      } else if(key.endsWith('_size')) value=Number(value)+'px';
      if (key && props[key] && (key.indexOf('_color') < 0 || isHex(value))) grid.style.setProperty(props[key],value);
    });
    var hasPadding=paddingSides.some(function(side){return !!grid.style.getPropertyValue('--alpdt-pad-'+side);});
    if(hasPadding) paddingSides.forEach(function(side){if(!grid.style.getPropertyValue('--alpdt-pad-'+side))grid.style.setProperty('--alpdt-pad-'+side,'0px');});
    grid.classList.toggle('alpdt-has-padding',hasPadding);
    var hasOuterPadding=paddingSides.some(function(side){return !!grid.style.getPropertyValue('--alpdt-outer-'+side);});
    if(hasOuterPadding)paddingSides.forEach(function(side){if(!grid.style.getPropertyValue('--alpdt-outer-'+side))grid.style.setProperty('--alpdt-outer-'+side,'0px');});
    grid.classList.toggle('alpdt-has-outer-padding',hasOuterPadding);
    if(grid.classList.contains('alpdt-clock-blood')) grid.dataset.alpdtBloodFont=(root.querySelector('#alpdt-blood-font')||{}).value==='nosifer'?'nosifer':'creepster';
    if (panelTransparent && panelTransparent.checked) grid.style.setProperty('--alpdt-flip-panel-bg','transparent');
    var digitCss=grid.style.getPropertyValue('--alpdt-digit-size')||'60px';
    var diameter=parseInt(grid.style.getPropertyValue('--alpdt-size')||'170',10);
    if(/^[0-9]+(?:\.[0-9]+)?px$/.test(digitCss)) {
      var size=parseFloat(digitCss);
      grid.style.setProperty('--alpdt-ring-diameter',Math.max(72,Math.min(300,Math.round(diameter*size/60)))+'px');
    } else {
      grid.style.setProperty('--alpdt-ring-diameter','clamp(72px, calc('+digitCss+' * '+(Math.round(diameter/60*10000)/10000)+'), 300px)');
    }
  }
  function startClock(target, unitString) {
    var chosen = unitString ? unitString.split(',') : ['days','hours','minutes','seconds'];
    var fullLabels = Object.assign({ years:'Years', months:'Months', days:'Days', hours:'Hours', minutes:'Minutes', seconds:'Seconds' }, i18n.unitLabels || {});
    var appearance = ['simple','glow','flip','slide','pulse','sweep','ring','celebration','confetti','blood','odometer','neon','liquid','matrix','orbit','glitch','morph','hourglass','snowfall','christmas_lights'].includes(clockStyle.value) ? clockStyle.value : 'simple';
    var labels = Object.assign({}, fullLabels);
    var horizontal = !!clockLayout && clockLayout.value === 'horizontal' && ['simple','glow','flip','slide','pulse','sweep','ring','odometer','neon','liquid','matrix','glitch','morph','celebration','confetti','snowfall','christmas_lights'].includes(appearance);
    labelInputs.forEach(function(input){var name=input.dataset.alpdtUnitLabel;if(Object.prototype.hasOwnProperty.call(labels,name)&&input.value.trim())labels[name]=input.value.trim().slice(0,32);});
    var grid = document.createElement('span');
    grid.className = 'alpdt-countdown alpdt-clock-grid alpdt-clock-' + appearance.replace(/_/g,'-') + (horizontal ? ' alpdt-layout-horizontal' : '');
    applyDesign(grid);
    if (appearance==='christmas_lights' && horizontal && grid.classList.contains('alpdt-has-padding')) {
      var topPad=parseFloat(grid.style.getPropertyValue('--alpdt-pad-top'));
      if(Number.isFinite(topPad) && topPad < 48) grid.classList.add('alpdt-xmas-behind');
    }
    if (['celebration','confetti'].includes(appearance)) { grid.setAttribute('data-alpdt-celebration-mode', celebrationMode ? celebrationMode.value : 'end'); grid.setAttribute('data-alpdt-celebration-duration', celebrationDuration ? celebrationDuration.value : '5'); grid.setAttribute('data-alpdt-celebration-message', celebrationMessage && celebrationMessage.value.trim() ? celebrationMessage.value.trim() : 'It’s time to celebrate!'); }
    if (appearance==='confetti') ['confetti','balloon'].forEach(function(kind){
      var palette=designInputs.filter(function(input){var key=input.dataset.alpdtDesign||'';var match=key.match(new RegExp('^'+kind+'_(\\d+)_color$'));return match && Number(match[1])<=extraColorCounts[kind] && isHex(input.value);}).map(function(input){return input.value;});
      if(paletteChanged(kind) && palette.length)grid.setAttribute('data-alpdt-'+kind+'-colors',palette.join(','));
    });
    grid.setAttribute('role','timer'); grid.setAttribute('aria-live','off');
    // Same static decorative DOM as PHP; CSS alone performs snowfall and bulb motion.
    if (appearance==='snowfall') {
      // The preview must use the SAME density, phase distribution and CSS movement
      // as the PHP frontend. Previously this used a periodic formula and made rows.
      var snow=document.createElement('span');snow.className='alpdt-snow-layer';snow.setAttribute('aria-hidden','true');
      var snowSymbols=['❄','❅','❆','✳','•'];
      var flakeCount=96;
      for(var si=0;si<flakeCount;si++){
        var f=document.createElement('span');f.className='alpdt-snowflake';
        var duration=19+Math.random()*18;
        var phase=(si+Math.random())/flakeCount;
        f.style.setProperty('--alpdt-flake-x',(Math.random()*100).toFixed(1)+'%');
        f.style.setProperty('--alpdt-flake-size',(4+Math.floor(Math.random()*13))+'px');
        f.style.setProperty('--alpdt-flake-duration',duration.toFixed(1)+'s');
        f.style.setProperty('--alpdt-flake-delay',(-phase*duration).toFixed(2)+'s');
        f.style.setProperty('--alpdt-flake-drift',(-39+Math.floor(Math.random()*79))+'px');
        f.style.setProperty('--alpdt-flake-sway',(-18+Math.floor(Math.random()*37))+'px');
        f.style.setProperty('--alpdt-flake-opacity',(.45+Math.random()*.47).toFixed(2));
        f.textContent=snowSymbols[Math.floor(Math.random()*snowSymbols.length)];
        snow.appendChild(f);
      }
      grid.appendChild(snow);
    } else if(appearance==='christmas_lights') {
      var strand=document.createElement('span');strand.className='alpdt-xmas-strand';strand.setAttribute('aria-hidden','true');
      var bulbInput=root.querySelector('#alpdt-bulb-count'),bulbCount=bulbInput && /^[0-9]{1,2}$/.test(bulbInput.value) ? Number(bulbInput.value):15;
      bulbCount=bulbCount>=8&&bulbCount<=60?bulbCount:15;
      var bulbs=['#FF4958','#21ED8E','#FFDB52','#63B5FF','#ED71F9'];
      for(var ci=1;ci<=12;ci++){var cinput=root.querySelector('[data-alpdt-design=\"christmas_'+ci+'_color\"]');if(cinput && isHex(cinput.value) && ci<=extraColorCounts.christmas){if(ci<=5)bulbs[ci-1]=cinput.value;else bulbs.push(cinput.value);}}
      for(var li=0;li<bulbCount;li++){var bulb=document.createElement('span');bulb.className='alpdt-xmas-light';bulb.style.setProperty('--alpdt-light-color',bulbs[li%bulbs.length]);strand.appendChild(bulb);}
      grid.appendChild(strand);
    }
    var cells = {};
    chosen.forEach(function(name) {
      if (!Object.prototype.hasOwnProperty.call(labels,name)) return;
      var cell = document.createElement('span'); cell.className='alpdt-clock-cell'; cell.setAttribute('data-alpdt-unit',name);
      var number = document.createElement('span'); number.className='alpdt-clock-number';
      var digit = document.createElement('span'); digit.className='alpdt-clock-digit'; digit.textContent='00';
      if (appearance === 'ring') number.innerHTML = '<svg class="alpdt-ring-svg" viewBox="0 0 120 120" focusable="false" aria-hidden="true"><circle class="alpdt-ring-track" cx="60" cy="60" r="49" pathLength="100"/><circle class="alpdt-ring-progress" cx="60" cy="60" r="49" pathLength="100"/></svg>';
      number.appendChild(digit); cell.appendChild(number);
      var lbl = document.createElement('span'); lbl.className='alpdt-clock-label'; lbl.textContent=labels[name];
      if (horizontal) {
        lbl.textContent = Array.from(labels[name])[0].toLocaleUpperCase();
        lbl.setAttribute('aria-hidden','true');
        var expanded=document.createElement('span');expanded.className='alpdt-visually-hidden';expanded.textContent=labels[name];cell.appendChild(lbl);cell.appendChild(expanded);
      } else { cell.appendChild(lbl); }
      grid.appendChild(cell); cells[name]={cell:cell,digit:digit};
    });
    preview.textContent=''; preview.appendChild(grid);
    function tick() {
      var now = Math.floor(Date.now()/1000), remaining = Math.max(0,Math.ceil(target-Date.now()/1000));
      var parts = typeof window.ALPDTCountdownParts==='function' ? window.ALPDTCountdownParts(now,target,i18n.timezone||'UTC',chosen) : {};
      chosen.forEach(function(name) {
        if (!cells[name]) return;
        var value = parts[name] === undefined ? 0 : parts[name];
        var text = horizontal || ['hours','minutes','seconds'].includes(name) ? String(value).padStart(2,'0') : String(value);
        var digit = cells[name].digit;
        var cycle = ({years:10,months:12,days:31,hours:24,minutes:60,seconds:60})[name] || 60;
        var progress = Math.max(0, Math.min(100, (value % cycle) * 100 / cycle));
        cells[name].cell.style.setProperty('--alpdt-progress', progress + '%');
        cells[name].cell.style.setProperty('--alpdt-hourglass-fill', String(progress / 100));
        var arc=cells[name].cell.querySelector('.alpdt-ring-progress'); if(arc) arc.style.strokeDashoffset=String(100-progress);
        if (window.ALPDTClockInitialize) window.ALPDTClockInitialize(cells[name].cell, digit);
        if (window.ALPDTClockDecorUpdate) window.ALPDTClockDecorUpdate(cells[name].cell,name,value,cycle,remaining>0,remaining);
        if (digit.textContent !== text) {
          var before = digit.textContent;
          digit.textContent=text;
          if (typeof window.ALPDTClockAnimate === 'function') window.ALPDTClockAnimate(cells[name].cell,digit,before,text);
          if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            cells[name].cell.classList.remove('alpdt-clock-change'); void cells[name].cell.offsetWidth; cells[name].cell.classList.add('alpdt-clock-change');
          }
        }
      });
      if (window.ALPDTClockLockWidths) window.ALPDTClockLockWidths(grid, false);
      if (remaining > 0 && ['celebration','confetti'].includes(appearance) && celebrationMode && celebrationMode.value === 'loop' && !document.hidden && (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches)) { var elapsed = Date.now(); if (!grid._alpdtLastLoop || elapsed-grid._alpdtLastLoop >= Math.max(8500,Number(celebrationDuration ? celebrationDuration.value : 5)*1000+2500)) { grid._alpdtLastLoop=elapsed; if (window.ALPDTCountdownCelebrate) window.ALPDTCountdownCelebrate(grid,{preview:true,duration:celebrationDuration ? Number(celebrationDuration.value) : 5}); } }
      if (remaining > 0) grid._alpdtSeenActive=true;
      if (remaining <= 0) {
        if (grid._alpdtSeenActive && !grid._alpdtFinished && ['celebration','confetti'].includes(appearance)) { grid._alpdtFinished=true; if(window.ALPDTCountdownCelebrate) window.ALPDTCountdownCelebrate(grid,{preview:false,duration:celebrationDuration ? Number(celebrationDuration.value) : 5}); }
        stopClock();
      }
    }
    tick(); if (clockTimer===null && target>Date.now()/1000) clockTimer=window.setInterval(tick,1000);
  }

  if (celebrationPreview) celebrationPreview.addEventListener('click',function(){
    var grid=preview.querySelector('.alpdt-clock-celebration,.alpdt-clock-confetti');
    if (grid && typeof window.ALPDTCountdownCelebrate === 'function') {
      window.ALPDTCountdownCelebrate(grid, { preview:true, duration:celebrationDuration ? Number(celebrationDuration.value) : 5 });
    }
  });

  // Saved shortcodes: named reusable configurations, not duplicates of Saved dates.
  var saveConfigButton = document.getElementById('alpdt-save-shortcode');
  var saveAsNewButton = document.getElementById('alpdt-save-as-new');
  var configDialog = document.getElementById('alpdt-save-shortcode-dialog');
  var configForm = document.getElementById('alpdt-save-shortcode-form');
  var configName = document.getElementById('alpdt-shortcode-name');
  var configSlug = document.getElementById('alpdt-shortcode-slug');
  var configOriginal = document.getElementById('alpdt-shortcode-original');
  var configCode = document.getElementById('alpdt-shortcode-to-save');
  var configCancel = document.getElementById('alpdt-save-shortcode-cancel');
  var configLastTrigger = null, editingConfig = null;
  function slugify(value) {
    return String(value).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9_-]+/g,'-').replace(/^-+|-+$/g,'').slice(0,55);
  }
  function parseStoredConfig(value) {
    if (!/^\[alpdt_time(?:\s+[^\[\]\r\n]*)?\]$/.test(value)) return null;
    var config = {}, pattern = /\s+([a-z][a-z0-9_]*)="([^"\[\]\r\n]*)"/g, match;
    while ((match = pattern.exec(value))) config[match[1]] = match[2];
    return config.type ? config : null;
  }
  function showSaveDialog(trigger) {
    if (!configDialog || !configForm) return;
    var badFont=invalidFontInput();
    if(badFont) {badFont.setCustomValidity(i18n.fontSizeInvalid||'Enter a valid font size for the selected unit.');badFont.focus();badFont.reportValidity();return;}
    var current = attrs();
    if (['elapsed','ordinal','anniversary_message'].includes(current.type) && !(current.id || current.from) ||
        current.type==='countdown' && (!(current.to || current.recurrence) || current.countdown_display==='custom' && !current.clock_units) ||
        current.type==='copyright' && current.copyright_style!=='current' && !(current.from || current.id) ||
        current.type==='smart_date' && current.expression==='date_difference' && !(current.from && current.to)) {
      status.textContent=i18n.shortcodeInvalid||'Check the date and options before saving.';status.classList.remove('alpdt-copy-ok');return;
    }
    configLastTrigger=trigger;configCode.value=code.value;
    configOriginal.value=editingConfig?editingConfig.slug:'';
    if(!editingConfig)delete configSlug.dataset.alpdtTouched;
    configName.value=editingConfig?editingConfig.name:'';
    configSlug.value=editingConfig?editingConfig.slug:'';
    configSlug.readOnly=!!editingConfig;
    configDialog.querySelector('h2').textContent=editingConfig?(i18n.updateShortcode||'Update saved shortcode'):(i18n.saveShortcode||'Save shortcode');
    if (typeof configDialog.showModal==='function') {configDialog.showModal();configName.focus();}
    else {configDialog.setAttribute('open','');configName.focus();}
  }
  if (configDialog && saveConfigButton) {
    saveConfigButton.addEventListener('click',function(){showSaveDialog(saveConfigButton);});
    configCancel.addEventListener('click',function(){configDialog.close();});
    configDialog.addEventListener('close',function(){if(configLastTrigger&&configLastTrigger.isConnected)configLastTrigger.focus();});
    configName.addEventListener('input',function(){if(!editingConfig && !configSlug.dataset.alpdtTouched)configSlug.value=slugify(configName.value);});
    configSlug.addEventListener('input',function(){configSlug.dataset.alpdtTouched='1';});
    configForm.addEventListener('submit',function(event){
      if(!configCode.value || !parseStoredConfig(configCode.value)){event.preventDefault();configName.setCustomValidity(i18n.shortcodeInvalid||'Invalid shortcode');configName.reportValidity();return;}
      configName.setCustomValidity('');
    });
  }
  function importStored(config) {
    var controls={from:from,to:to,id:saved,mode:mode,unit:unit,offset:offset,format:format,countdown_display:countdownDisplay,copyright_style:copyrightStyle,clock_style:clockStyle,clock_layout:clockLayout,lights_mode:lightsMode,celebration_duration:celebrationDuration,celebration_mode:celebrationMode,celebration_message:celebrationMessage,before:msgBefore,on:msgOn,after:msgAfter,expression:expression,hemisphere:hemisphere,recurrence:recurrence,repeat_time:repeatTime,repeat_weekday:repeatWeekday,repeat_day:repeatDay,end_action:endAction,end_message:endMessage,end_url:endUrl};
    type.value=config.type;
    if (from) from.value=config.from||'2000';
    if (diffFrom) diffFrom.value=(config.from&&/^\d{4}-\d{2}-\d{2}$/.test(config.from))?config.from:'2026-01-01';
    if (diffTo) diffTo.value=(config.to&&/^\d{4}-\d{2}-\d{2}$/.test(config.to))?config.to:'2026-12-31';
    if (saved) saved.value=config.id||'';
    if (to) to.value=config.to||'2027-01-01T12:00';
    var base={mode:'anniversary',unit:'years',offset:'0',format:'j F Y',countdown_display:'days',copyright_style:'range',expression:'today',hemisphere:'north',recurrence:'none',repeat_time:'12:00',repeat_weekday:'1',repeat_day:'1',end_action:'none',end_message:'',end_url:'',clock_style:'simple',clock_layout:'default',lights_mode:'multicolor',celebration_duration:'5',celebration_mode:'end',celebration_message:'',before:'',on:'',after:''};
    Object.keys(base).forEach(function(key){
      var el=controls[key];if(!el)return;
      var storedValue=config[key]!==undefined?config[key]:base[key];
      // Older minimalist/promo shortcodes remain renderable, but edit as the new horizontal layout.
      if (key==='clock_style' && ['minimalist','promo_dark','promo_light','promo_outline'].includes(storedValue)) storedValue='simple';
      if (key==='clock_layout' && ['minimalist','promo_dark','promo_light','promo_outline'].includes(config.clock_style)) storedValue='horizontal';
      el.value=storedValue;
    });
    if (txt) txt.checked=config.text!=='false';
    if (live) live.checked=config.live==='true';
    if (config.clock_units) {
      var selected=config.clock_units.split(',');unitChecks.forEach(function(input){input.checked=selected.includes(input.dataset.alpdtClockUnit);});
    } else unitChecks.forEach(function(input){input.checked=['days','hours','minutes','seconds'].includes(input.dataset.alpdtClockUnit);});
    labelInputs.forEach(function(input){input.value=config['label_'+input.dataset.alpdtUnitLabel]||'';});
    designInputs.forEach(function(input){
      var key=input.dataset.alpdtDesign;
      if(config[key]!==undefined){input.value=config[key];input.dataset.alpdtTouched='1';}
      else if(key==='digit_size'||key==='label_size'){input.value=key==='digit_size'?'60':'14';delete input.dataset.alpdtTouched;}
      else if(key==='clock_width'){input.value='auto';delete input.dataset.alpdtTouched;}
      else if(key==='clock_radius'||key==='bulb_card_gap'||key==='bulb_count'||key==='glow_background_color'||/^(?:clock_padding(?:_(?:top|right|bottom|left))?|countdown_padding_(?:top|right|bottom|left))$/.test(key)){input.value='';delete input.dataset.alpdtTouched;}
      else if(/^(?:confetti|balloon)_(?:[1-9]|10)_color$/.test(key) || /^christmas_(?:[1-9]|1[0-2])_color$/.test(key)){input.value=input.defaultValue;delete input.dataset.alpdtTouched;}
      if(key==='digit_size'||key==='label_size')fontSet(input,input.value);
      var picker=root.querySelector('[data-alpdt-color-picker="'+key+'"]');
      if(picker && picker.alpdtSetColor && config[key] && config[key]!=='transparent')picker.alpdtSetColor(config[key]);
    });
    ['confetti','balloon','christmas'].forEach(function(kind){
      var count=kind==='christmas'?5:4;
      for(var x=count+1;x<=(kind==='christmas'?12:10);x++)if(config[kind+'_'+x+'_color'])count=x;
      extraColorCounts[kind]=count;
    });
    syncExtraColorFields();
    // Legacy global padding is displayed as four equal sides. Saved individual sides take precedence.
    if(config.clock_padding!==undefined && !paddingSides.some(function(side){return config['clock_padding_'+side]!==undefined;})) {
      paddingSides.forEach(function(side){if(paddingInputs.card[side])paddingInputs.card[side].value=config.clock_padding;});
    }
    resetPaddingLinks();
    if (panelTransparent)panelTransparent.checked=config.panel_color==='transparent';
    Array.prototype.forEach.call(root.querySelectorAll('.alpdt-field select'),function(select){if(select.alpdtRepaint)select.alpdtRepaint();});
    activate('create',false);activateClockTab('units',false);refresh();
    if(saveConfigButton)saveConfigButton.textContent=i18n.updateShortcode||'Update saved shortcode';
    if(saveAsNewButton)saveAsNewButton.hidden=false;
  }
  Array.prototype.forEach.call(root.querySelectorAll('[data-alpdt-edit-shortcode]'),function(button){
    button.addEventListener('click',function(){
      var config=parseStoredConfig(button.dataset.code||'');if(!config)return;
      editingConfig={slug:button.dataset.slug,name:button.dataset.name,code:button.dataset.code};
      importStored(config);
      saveConfigButton.focus();
    });
  });
  if(saveAsNewButton)saveAsNewButton.addEventListener('click',function(){editingConfig=null;delete configSlug.dataset.alpdtTouched;saveConfigButton.textContent=i18n.saveShortcode||'Save shortcode';saveAsNewButton.hidden=true;showSaveDialog(saveAsNewButton);});
  Array.prototype.forEach.call(root.querySelectorAll('[data-alpdt-open-generator]'),function(button){
    button.addEventListener('click',function(){editingConfig=null;delete configSlug.dataset.alpdtTouched;if(saveAsNewButton)saveAsNewButton.hidden=true;if(saveConfigButton)saveConfigButton.textContent=i18n.saveShortcode||'Save shortcode';activate('create',false);type.focus();});
  });
  refresh();
  // One transient, viewport-centered SafeEdit-style confirmation for every copy action.
  // Non-modal by design: it does not steal focus, block the UI, or alter card dimensions.
  var copyNotice = document.getElementById('alpdt-copy-notice');
  var copyNoticeText = document.getElementById('alpdt-copy-notice-text');
  var copyNoticeTimer = null;
  function showCopyNotice(kind, succeeded) {
    if (!copyNotice || !copyNoticeText) return;
    if (copyNoticeTimer !== null) window.clearTimeout(copyNoticeTimer);
    copyNotice.classList.toggle('alpdt-copy-notice-error', !succeeded);
    copyNoticeText.textContent = succeeded ?
      (kind === 'variable' ? (i18n.anniversaryVariableCopied || 'Variable copied.') : (i18n.copied || 'Shortcode copied.')) :
      (i18n.copyFallback || 'Select the text and copy it manually.');
    copyNotice.hidden = false;
    // Success: exactly two seconds. Failures remain readable longer for manual copy.
    copyNoticeTimer = window.setTimeout(function () {
      copyNotice.hidden = true; copyNoticeText.textContent = ''; copyNoticeTimer = null;
    }, succeeded ? 2000 : 6000);
  }
  copy.addEventListener('click', function () {
    if (!code.value) return;
    function fallback() { code.focus(); code.select(); showCopyNotice('shortcode', false); }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(code.value).then(function () { showCopyNotice('shortcode', true); }).catch(fallback);
    } else fallback();
  });
  root.addEventListener('click', function (event) {
    var button = event.target.closest('[data-alpdt-copy-value]');
    if (!button || !root.contains(button)) return;
    var text = button.getAttribute('data-alpdt-copy-value') || '';
    if (!text) return;
    var kind = button.dataset.alpdtCopyKind === 'variable' || button.classList.contains('alpdt-anniversary-copy') ? 'variable' : 'shortcode';
    function fallback() {
      var action = button.closest('.alpdt-copy-action');
      var codeNode = action && action.previousElementSibling && action.previousElementSibling.tagName === 'CODE' ? action.previousElementSibling : (action && action.parentElement && action.parentElement.querySelector('code'));
      if (codeNode) {
        var selection = window.getSelection(), range = document.createRange();
        range.selectNodeContents(codeNode); selection.removeAllRanges(); selection.addRange(range);
      }
      showCopyNotice(kind, false);
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function () { showCopyNotice(kind, true); }).catch(fallback);
    } else fallback();
  });
  code.addEventListener('input', function () { status.textContent = ''; status.classList.remove('alpdt-copy-ok'); });
  [type, from, to, diffFrom, diffTo, live, saved, mode, unit, txt, offset, format, countdownDisplay, copyrightStyle, clockStyle, clockLayout, lightsMode, celebrationMessage, celebrationDuration, celebrationMode, msgBefore, msgOn, msgAfter, expression, hemisphere, recurrence, repeatTime, repeatWeekday, repeatDay, endAction, endMessage, endUrl].concat(unitChecks,designInputs,labelInputs).filter(Boolean).forEach(function (field) {
    field.addEventListener('change', function () { status.textContent = ''; status.classList.remove('alpdt-copy-ok'); });
  });

  var editForm = document.getElementById('alpdt-milestone-form');
  var original = document.getElementById('alpdt-original');
  var slug = document.getElementById('alpdt-slug');
  var label = document.getElementById('alpdt-label');
  var date = document.getElementById('alpdt-date');
  var saveButton = document.getElementById('alpdt-save');
  var cancelEdit = document.getElementById('alpdt-cancel-edit');
  var editState = document.getElementById('alpdt-edit-state');
  var editHeading = document.getElementById('alpdt-form-heading');
  var initialHeading = editHeading ? editHeading.textContent : '';
  var lastEditTrigger = null;
  if (editForm && original && slug && label && date && cancelEdit) {
    function resetEdit(restoreFocus) {
      editForm.reset(); original.value = ''; slug.readOnly = false;
      saveButton.textContent = i18n.saveDate; cancelEdit.hidden = true; editState.hidden = true;
      editHeading.textContent = initialHeading;
      if (restoreFocus && lastEditTrigger) lastEditTrigger.focus();
      lastEditTrigger = null;
    }
    Array.prototype.forEach.call(root.querySelectorAll('[data-alpdt-edit]'), function (button) {
      button.addEventListener('click', function () {
        lastEditTrigger = button;
        activate('dates', false);
        original.value = button.dataset.slug;
        slug.value = button.dataset.slug;
        slug.readOnly = true;
        label.value = button.dataset.label;
        date.value = button.dataset.date;
        editState.hidden = false;
        cancelEdit.hidden = false;
        saveButton.textContent = i18n.saveChanges;
        editHeading.textContent = i18n.editDate;
        label.focus();
      });
    });
    cancelEdit.addEventListener('click', function () { resetEdit(true); });
  }

  var deleteForms = Array.prototype.slice.call(root.querySelectorAll('form[data-alpdt-delete]'));
  if (!deleteForms.length) return;
  var dialog = document.createElement('dialog');
  dialog.className = 'alpdt-dialog'; dialog.setAttribute('aria-labelledby', 'alpdt-dialog-title');
  var title = document.createElement('h2'); title.id = 'alpdt-dialog-title'; title.textContent = i18n.deleteTitle;
  var message = document.createElement('p'); message.id = 'alpdt-dialog-description'; message.textContent = i18n.deleteHelp;
  dialog.setAttribute('aria-describedby', message.id);
  var icon = document.createElement('span'); icon.className = 'alpdt-dialog-warning'; icon.setAttribute('aria-hidden', 'true'); icon.textContent = '!';
  var actions = document.createElement('div'); actions.className = 'alpdt-dialog-actions';
  var cancel = document.createElement('button'); cancel.type = 'button'; cancel.className = 'button'; cancel.textContent = i18n.cancel;
  var confirmDelete = document.createElement('button'); confirmDelete.type = 'button'; confirmDelete.className = 'button alpdt-confirm-danger'; confirmDelete.textContent = i18n.confirmDelete;
  actions.appendChild(cancel); actions.appendChild(confirmDelete); dialog.appendChild(icon); dialog.appendChild(title); dialog.appendChild(message); dialog.appendChild(actions); root.appendChild(dialog);
  var pendingForm = null, originButton = null, scrim = null;
  function restoreFocus() { if (originButton && originButton.isConnected) originButton.focus(); originButton = null; }
  function closeDialog() {
    if (typeof dialog.close === 'function' && dialog.open && !dialog.classList.contains('alpdt-dialog-fallback')) dialog.close();
    else {
      dialog.removeAttribute('open'); dialog.classList.remove('alpdt-dialog-fallback');
      if (scrim) { scrim.remove(); scrim = null; }
      restoreFocus();
    }
    pendingForm = null;
  }
  deleteForms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault(); pendingForm = form; originButton = form.querySelector('button');
      var row = form.closest('.alpdt-preset'); var name = row && row.querySelector('strong');
      var isShortcode = form.dataset.alpdtDeleteKind === 'shortcode';
      title.textContent = (isShortcode ? (i18n.deleteShortcodeTitle || 'Delete saved shortcode?') : i18n.deleteTitle) + (name ? ' ' + name.textContent : '');
      message.textContent = isShortcode ? (i18n.deleteShortcodeHelp || 'Pages using this identifier will stop working.') : i18n.deleteHelp;
      if (typeof dialog.showModal === 'function') dialog.showModal();
      else {
        scrim = document.createElement('div'); scrim.className = 'alpdt-dialog-scrim'; root.appendChild(scrim);
        dialog.setAttribute('open', ''); dialog.classList.add('alpdt-dialog-fallback'); dialog.setAttribute('role', 'dialog'); dialog.setAttribute('aria-modal', 'true');
      }
      cancel.focus();
    });
  });
  dialog.addEventListener('close', restoreFocus);
  dialog.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape' && dialog.classList.contains('alpdt-dialog-fallback')) { ev.preventDefault(); closeDialog(); }
    if (ev.key === 'Tab') {
      if (ev.shiftKey && document.activeElement === cancel) { ev.preventDefault(); confirmDelete.focus(); }
      else if (!ev.shiftKey && document.activeElement === confirmDelete) { ev.preventDefault(); cancel.focus(); }
    }
  });
  cancel.addEventListener('click', closeDialog);
  confirmDelete.addEventListener('click', function () {
    var toSubmit = pendingForm; closeDialog();
    if (toSubmit) HTMLFormElement.prototype.submit.call(toSubmit);
  });
}());
