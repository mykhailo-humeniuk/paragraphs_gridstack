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
  
  function _gatherInfo() {
  
  }

  /**
   * Implements grid and backbone collections on node edit page.
   */
  Drupal.behaviors.gridstackField = {
    attach: function (context, settings) {
      const fieldGridstack = $('.grid-stack');
      let options = {};

      Array.prototype.forEach.call(fieldGridstack, gridHtml => {

      });
      
      $('.grid-stack').gridstack({
        cellHeight: 50,
        verticalMargin: 0,
        width: 12
      });
      
      
      if (typeof settings.gridStack !== 'undefined') {
        options = settings.gridStack.settings;
        // fieldGridstack.gridstack(options);
        options = Object.values(options);
        options.forEach(value => {
          $('div[fid = ' + value.field_id + ']').gridstack(value);
        });
        // $('div[fid = ' + options.field_id + ']').gridstack(options);
      }

      // Fill in JSON field with parameters from grid items.
      const $gridContainer = $('.grid-stack');
      let $gridItems = $gridContainer.find('.grid-stack-item.ui-draggable.ui-resizable');
      let jsonFieldData = {};
      // jsonFieldData[fid] = {};

      jsonFieldData.items = [];
      jsonFieldData.settings = options;
      // jsonFieldData.fid = fid;

      // Warm up cache on page load and add new items.
      // @TODO need rewrite loaded logic to document ready.
      if (!loaded) {
        if ($gridItems.length) {
          $gridItems.each(function (key, item) {
            var obj = {
              x: $(item).data('gs-x'),
              y: $(item).data('gs-y'),
              width: $(item).data('gs-width'),
              height: $(item).data('gs-height'),
              delta: $(item).data('delta')
            };
            jsonFieldData.items.push(obj);
          });
          console.log('KUUUUUUUUUUUUURWA WARMING');
          _saveParagraphPosition(jsonFieldData);
        }
        loaded = true;
      }
      
      // $('.field--widget-paragraphs-gridstack-widget').once('save-item', function () {
      //   // Add custom element with value of item height.
      //   console.log('GGGGGGGGGGGGGGGGGGGGGG');
      //   console.log('$gridItems', $gridItems);
      //   $gridItems.each(function (key, item) {
      //     let height = $(item).data('gs-height');
      //     height = 'Height: ' + (height * 50) + 'px';
      //     // $(item).find('.grid-stack-item-content').prepend('<div class="height-counter">' + height + '</div>');
      //     $(item).prependTo('<div class="height-counter">' + height + '</div>');
      //   });
      //
      //   $(this).on('change', function(event, items) {
      //     console.log('CHANGE KURWA');
      //     if(items != undefined) {
      //       console.log('IN IF KURWA');
      //
      //
      //       $(items).each(function(i) {
      //         console.log('CE THIS KURWA', this);
      //         console.log('CE THIS KURWA2', this.el[0]);
      //         var obj = {
      //           x: this.x,
      //           y: this.y,
      //           width: this.width,
      //           height: this.height,
      //           delta: this.el[0].dataset.delta
      //         };
      //
      //         if ((jsonFieldData.length === 0) || (items[i].el[0].dataset.delta === jsonFieldData.length)) {
      //           jsonFieldData.items.push(obj);
      //         }
      //         else {
      //           jsonFieldData.items[this.el[0].dataset.delta].x = this.x;
      //           jsonFieldData.items[this.el[0].dataset.delta].y = this.y;
      //           jsonFieldData.items[this.el[0].dataset.delta].width = this.width;
      //           jsonFieldData.items[this.el[0].dataset.delta].height = this.height;
      //         }
      //
      //         // Update custom element with value of item height.
      //         var height = this.height;
      //         var $heightContainer = $(items[i].el[0]).find('.height-counter');
      //         height = 'Height: ' + (height * 50) + 'px';
      //         $heightContainer.text(height);
      //       });
      //       _saveParagraphPosition(jsonFieldData);
      //     }
      //   });
      // });
      
      $('.field--widget-paragraphs-gridstack-widget').once('save-item').each(function () {
          const uniqueKey = $(this).find('.form-item-grid').attr('fid');
          jsonFieldData.items[uniqueKey] = [];
          console.log('KEY', uniqueKey);
        
          // Add custom element with value of item height.
          $gridItems.each(function (key, item) {
            let height = $(item).data('gs-height');
            height = 'Height: ' + (height * 50) + 'px';
            // $(item).find('.grid-stack-item-content').prepend('<div class="height-counter">' + height + '</div>');
            $(item).prependTo('<div class="height-counter">' + height + '</div>');
          });
          
          $(this).on('change.grid', function(event, items) {
            if(items != undefined) {
              $(items).each(function(i) {
                var obj = {
                  x: this.x,
                  y: this.y,
                  width: this.width,
                  height: this.height,
                  delta: this.el[0].dataset.delta
                };
        
                if ((jsonFieldData.length === 0) || (items[i].el[0].dataset.delta === jsonFieldData.length)) {
                  jsonFieldData.items.push(obj);
                }
                else {
                  console.log('jsonFieldData', jsonFieldData);
                  jsonFieldData.items[uniqueKey][this.el[0].dataset.delta] = {}; //@TODO ?????
                  
                  jsonFieldData.items[uniqueKey][this.el[0].dataset.delta].x = this.x;
                  jsonFieldData.items[uniqueKey][this.el[0].dataset.delta].y = this.y;
                  jsonFieldData.items[uniqueKey][this.el[0].dataset.delta].width = this.width;
                  jsonFieldData.items[uniqueKey][this.el[0].dataset.delta].height = this.height;
                }
        
                // Update custom element with value of item height.
                var height = this.height;
                var $heightContainer = $(items[i].el[0]).find('.height-counter');
                height = 'Height: ' + (height * 50) + 'px';
                $heightContainer.text(height);
              });
              _saveParagraphPosition(jsonFieldData);
            }
          });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);



// выбираем целевой элемент
let targets = document.querySelectorAll('.field--widget-paragraphs-gridstack-widget');
// console.log('target', target);

// создаём экземпляр MutationObserver
let observer = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    let gridItemsR = mutation.target.querySelectorAll('.grid-stack-item.ui-draggable.ui-resizable');
    gridItemsR.forEach(itemBu => {
      console.log(itemBu.dataset);
    });
  });
});

// конфигурация нашего observer:
const config = { attributes: true, childList: true, characterData: true };

// передаём в качестве аргументов целевой элемент и его конфигурацию
targets.forEach(gridHtml => {
  observer.observe(gridHtml, config);
});



const gridFields = document.querySelectorAll('.field--widget-paragraphs-gridstack-widget');
console.log(gridFields);

// .....
gridFields.forEach(field => {

});
