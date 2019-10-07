/**
 * @file
 * Provides GridStack loaders.
 */

(function ($, Drupal, settings) {
  'use strict';

  /**
   * Helper function.
   *
   * Add data to json field and send to drupal callback.
   */
  /**
   * Helper function.
   *
   * Add data to json field and send to drupal callback.
   *
   * @param array jsonData
   *   Information about grid in json format.
   * @private
   */
  function _saveParagraphPosition(jsonData) {
    const { baseUrl, pathPrefix } = drupalSettings.path;
    const href = `${baseUrl}${pathPrefix}grid_update`;
    const post = "grid_items=" + JSON.stringify(jsonData);

    // Send data to drupal side.
    $.ajax({
      url: href,
      type: "POST",
      dataType: 'json',
      data: post,
      success: function (data) {
      }
    });
    $('#edit-field-paragraphs-gridstack-json-wrapper textarea').val(JSON.stringify(jsonData));
  }

  function _gatherInfo(obj) {
    let { gridItems: items, jsonFieldData, uniqueKey } = obj;
    items.forEach((item) => {
      let props = item.dataset;
      let obj = {
        x: props.gsX,
        y: props.gsY,
        width: props.gsWidth,
        height: props.gsHeight,
        delta: props.delta
      };

      if ((jsonFieldData.length === 0) || (props.delta === jsonFieldData.length)) {
        jsonFieldData.items[uniqueKey].push(obj);
      }
      else {
        jsonFieldData.items[uniqueKey][props.delta] = {}; //@TODO ?????

        jsonFieldData.items[uniqueKey][props.delta].x = props.gsX;
        jsonFieldData.items[uniqueKey][props.delta].y = props.gsY;
        jsonFieldData.items[uniqueKey][props.delta].width = props.gsWidth;
        jsonFieldData.items[uniqueKey][props.delta].height = props.gsHeight;
      }

      // Update custom element with value of item height.
      let height = props.gsHeight;
      let $heightContainer = $(item).find('.height-counter');
      let cell_height = settings.gridStack.settings[uniqueKey].cell_height;
      let verticalMargin =  settings.gridStack.settings[uniqueKey].verticalMargin;
      height = 'Height: ' + (parseInt(height * cell_height) + parseInt(verticalMargin * (height - 1))) + 'px';
      $heightContainer.text(height);
    });

    jsonFieldData.settings[uniqueKey] = settings.gridStack.settings[uniqueKey];

    _saveParagraphPosition(jsonFieldData);
  }

  /**
   * ---------------------------------------------------------------------------------------------
   * @type {{}}
   */

  let options = {};

  Drupal.behaviors.gridstackField = {
    attach: function (context, settings) {
      let options = {};
      // Gridstack init.
      if (typeof settings.gridStack !== 'undefined') {
        options = settings.gridStack.settings;
        options = Object.values(options);
        options.forEach((value) => {
          var $grid = $('.grid-stack[fid = ' + value.field_id + ']');
          $grid.gridstack(value);
          if (value.width !== 'undefined' && !$grid.hasClass('width-' + value.width) ) {
            $grid.addClass('width-' + value.width);
          }
        });
      }

      // @TODO need to refactor this.
      // SHow hide grid.
      if (!$('#grid-settings').length) {
        $('.form-item-grid').show();
      }
      else {
        $('.form-item-grid').hide();
      }

      // Ugly fix due to issues with saving empty node.
      if ($(context).hasClass('grid-settings-replacement')) {
        $('.field--widget-paragraphs-gridstack-widget').trigger('change');
      }

      // Choose element
      const gridFields = document.querySelectorAll('.field--widget-paragraphs-gridstack-widget');
      // pass arguments
      gridFields.forEach((gridHtml, key) => {
        let gridItems = gridHtml.querySelectorAll('.grid-stack-item');
        // Add custom element with value of item height.
        if (gridItems.length) {
          gridItems.forEach((item) => {
            if (!$(item).find('.height-counter').length) {
              let height = $(item).data('gs-height');
              height = 'Height: ' + (parseInt(height * options[key].cell_height) + parseInt(options[key].verticalMargin * (height - 1))) + 'px';
              $(item).find('.grid-stack-item-content').prepend('<div class="height-counter">' + height + '</div>');
            }
          });
        }
      });
    }
  };

  $(document).ready(() => {
    let data = $('#edit-field-paragraphs-gridstack-json-wrapper textarea').val();
    if (data.length) {
      data = JSON.parse(data);
      let settingsKeys = Object.keys(data.settings);
      let settingsValues = Object.values(data.settings);

      settingsValues.forEach((v, i) => {
        $('.grid-stack[fid = ' + settingsKeys[i] + ']').gridstack(v);
      });

      // Fix for avoiding problems with adding new items after failed validation.
      if (typeof settings.gridStack == 'undefined') {
        settings.gridStack = data;
      }
    }
  });

  let jsonFieldData = {};

  // Create obj structure.
  jsonFieldData.items = {};
  jsonFieldData.settings = options;
  // jsonFieldData.fid = fid;

  // Choose element
  const gridFields = document.querySelectorAll('.field--widget-paragraphs-gridstack-widget');

  // Create instance of MutationObserver
  // Insert/remove dom elements event listener.
  let observer = new MutationObserver(function(mutations, observer) {
    mutations.forEach((mutation) => {

      // No need to do any actions on appearing/disappearing ajax throbbers.
      if (mutation.target.querySelector('.confirm-remove')) {
        return;
      }

      if (mutation.type === 'childList') {
        if (mutation.target.querySelectorAll('.form-item-grid').length) {
          let gridItems = mutation.target.querySelectorAll('.grid-stack-item.ui-draggable.ui-resizable');

          const uniqueKey = mutation.target.querySelectorAll('.form-item-grid')[0].getAttribute('fid');
          jsonFieldData.items[uniqueKey] = {};

          _gatherInfo({gridItems, jsonFieldData, uniqueKey});
        }
      }
    });
  });

  // configure our observer:
  const config = { attributes: false, childList: true, characterData: false, subtree: true };

  // pass arguments
  gridFields.forEach((gridHtml) => {
    const uniqueKey = gridHtml.querySelectorAll('.form-item-grid')[0].getAttribute('fid');
    let gridItems = gridHtml.querySelectorAll('.grid-stack-item');
    jsonFieldData.items[uniqueKey] = [];

    // Observe insert/remove events.
    observer.observe(gridHtml, config);

    // Other change events.
    gridHtml.onchange = item => {
      if ($(item.target).is('.form-select, .form-text, .form-file, .form-textarea, .form-jquery_colorpicker, .form-checkbox, .form-radio')) {
        return;
      }
      let gridItems = item.target.querySelectorAll('.grid-stack-item.ui-draggable.ui-resizable');
      _gatherInfo({gridItems, jsonFieldData, uniqueKey});
    };
  });

})(jQuery, Drupal, drupalSettings);
