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
  // function _saveParagraphPosition(jsonData) {
  //   console.log('jsonData: ', jsonData);
  //   const { basePath, pathPrefix } = Drupal.settings;
  //   const href = `${basePath}${pathPrefix}grid_update`;
  //   const post = "grid_items=" + JSON.stringify(jsonData);
  //
  //   // Send data to drupal side.
  //   $.ajax({
  //     url: href,
  //     type: "POST",
  //     dataType: 'json',
  //     data: post,
  //     success: function (data) {
  //     }
  //   });
  // }

  /**
   * Implements grid and backbone collections on node edit page.
   */
  Drupal.behaviors.gridstackField = {
    attach: function (context, settings) {
      // const fieldGridstack = $('.form-item-grid');
      // let options = {};
      //
      // Array.prototype.forEach.call(fieldGridstack, gridHtml => {
      //   console.log(gridHtml);
      //   console.log($(gridHtml).attr('fid'));
      // });
      //
      
      // console.log('RRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR');
      
      $('.grid-stack').gridstack({
        cellHeight: 200,
        verticalMargin: 0,
        width: 12
      });
      
      
      
      // if (typeof settings.gridstack_field !== 'undefined') {
      //   options = settings.gridstack_field.settings;
      //   // fieldGridstack.gridstack(options);
      //   options = Object.values(options);
      //   options.forEach(value => {
      //     $('div[fid = ' + value.field_id + ']').gridstack(value);
      //   });
      //   // $('div[fid = ' + options.field_id + ']').gridstack(options);
      // }
      //
      // // Fill in JSON field with parameters from grid items.
      // const $gridContainer = $('.form-item-grid');
      // let $gridItems = $gridContainer.find('.grid-stack-item.ui-draggable.ui-resizable');
      // let jsonFieldData = {};
      // // jsonFieldData[fid] = {};
      //
      // jsonFieldData.items = [];
      // jsonFieldData.settings = options;
      // // jsonFieldData.fid = fid;
      //
      // // Warm up cache on page load and add new items.
      // if ($gridItems.length) {
      //   $gridItems.each(function (key, item) {
      //     var obj = {
      //       x: $(item).data('gs-x'),
      //       y: $(item).data('gs-y'),
      //       width: $(item).data('gs-width'),
      //       height: $(item).data('gs-height'),
      //       delta: $(item).data('delta')
      //     };
      //     jsonFieldData.items.push(obj);
      //   });
      //   _saveParagraphPosition(jsonFieldData);
      // }
      //
      // $('.field-widget-paragraphs-gridstack').once('save-item', function () {
      //   // Add custom element with value of item height.
      //   $gridItems.each(function (key, item) {
      //     let height = $(item).data('gs-height');
      //     height = 'Height: ' + (height * 50) + 'px';
      //     $(item).find('.grid-stack-item-content').prepend('<div class="height-counter">' + height + '</div>');
      //   });
      //
      //   $(this).on('change', function(event, items) {
      //     if(items != undefined) {
      //       $(items).each(function(i) {
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
    }
  };
})(jQuery, Drupal, Drupal.settings);
