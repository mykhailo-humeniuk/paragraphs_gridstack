/**
 * @file
 * Provides GridStack loaders.
 */

(function ($, Drupal, settings) {
  'use strict';
  
  var loaded = false;
  
  /**
   * Helper function.
   *
   * Add data to json field and send to drupal callback.
   */
  function _saveParagraphPosition(jsonData) {
    console.log('jsonData', jsonData);

    const { baseUrl, pathPrefix } = drupalSettings.path;
    const href = `${baseUrl}${pathPrefix}grid_update`;
    const post = "grid_items=" + JSON.stringify(jsonData);

    console.log('JSON.stringify(jsonData)', JSON.stringify(jsonData));

    // Send data to drupal side.
    $.ajax({
      url: href,
      type: "POST",
      dataType: 'json',
      data: post,
      success: function (data) {
      }
    });
  }
  
  function _gatherInfo(obj) {
    let { gridItems: items, jsonFieldData, uniqueKey } = obj;

    console.log('items', items);
    console.log('jsonFieldData', jsonFieldData);
    console.log('uniqueKey', uniqueKey);
    console.log(typeof(items));
    items.forEach(item => {
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
      height = 'Height: ' + (height * 50) + 'px';
      $heightContainer.text(height);
    });
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
        options.forEach(value => {
          $('.grid-stack[fid = ' + value.field_id + ']').gridstack(value);
        });
      }
    }
  };


  const fieldGridstack = document.querySelectorAll('.grid-stack');

  let jsonFieldData = {};
  // jsonFieldData[fid] = {};

  // Fill in JSON field with parameters from grid items.
  if (fieldGridstack.length) {
    let gridItems = fieldGridstack.querySelectorAll('.grid-stack-item.ui-draggable.ui-resizable');

    // Warm up cache on page load and add new items.
    // @TODO need rewrite loaded logic to document ready.
    if (!loaded) {
      if (gridItems.length) {
        gridItems.each(item => {
          var obj = {
            x: $(item).data('gs-x'),
            y: $(item).data('gs-y'),
            width: $(item).data('gs-width'),
            height: $(item).data('gs-height'),
            delta: $(item).data('delta')
        };
          jsonFieldData.items.push(obj);
        });
        _saveParagraphPosition(jsonFieldData);
      }
      loaded = true;
    }
  }

  // Create obj structure.
  jsonFieldData.items = {};
  jsonFieldData.settings = options;
  // jsonFieldData.fid = fid;


  // выбираем целевой элемент
  const gridFields = document.querySelectorAll('.field--widget-paragraphs-gridstack-widget');

  // создаём экземпляр MutationObserver
  // Insert/remove dom elements event listener.
  let observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      let gridItems = mutation.target.querySelectorAll('.grid-stack-item.ui-draggable.ui-resizable');

      const uniqueKey = mutation.target.querySelectorAll('.form-item-grid')[0].getAttribute('fid');
      jsonFieldData.items[uniqueKey] = {};

      _gatherInfo({gridItems, jsonFieldData, uniqueKey});

      // DO SOMETHING ON INSERT/DELETE.
      gridItems.forEach(itemBu => {
        // DO SOMETHING ON INSERT/DELETE.
      });
    });
  });

  // конфигурация нашего observer:
  const config = { attributes: true, childList: true, characterData: true };

  // передаём в качестве аргументов целевой элемент и его конфигурацию
  gridFields.forEach(gridHtml => {
    const uniqueKey = gridHtml.querySelectorAll('.form-item-grid')[0].getAttribute('fid');
    let gridItems = gridHtml.querySelectorAll('.grid-stack-item.ui-draggable.ui-resizable');
    jsonFieldData.items[uniqueKey] = [];

    // Observe insert/remove events.
    observer.observe(gridHtml, config);

    // Add custom element with value of item height.
    if (gridItems.length) {
      gridItems.forEach(item => {
        console.log('ADD CUSTOM HEIGHT ITEM', item);
        let height = $(item).data('gs-height');
        height = 'Height: ' + (height * 50) + 'px';
        $(item).prependTo('<div class="height-counter">' + height + '</div>');
      });
    }

    // Other change events.
    gridHtml.onchange = item => {
      let gridItems = item.target.querySelectorAll('.grid-stack-item.ui-draggable.ui-resizable');

      _gatherInfo({gridItems, jsonFieldData, uniqueKey});

      gridItems.forEach(itemBu => {
        // DO THOMETHING ON CHANGE
      });
    };
  });




})(jQuery, Drupal, drupalSettings);



