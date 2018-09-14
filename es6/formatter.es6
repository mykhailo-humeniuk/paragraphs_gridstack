/**
 * @file
 * Provides GridStack loaders for view page.
 */

(function ($, Drupal, settings) {
  'use strict';

  function _getGridHeight(items) {
    return _.reduce(items, function (memo, n) {
      var dataGsY = parseInt($(n).attr('data-gs-y'));
      var dataGsHeight = parseInt($(n).attr('data-gs-height'));
      return Math.max(memo, dataGsY + dataGsHeight);
    }, 0);
  }
  /**
   * Implements grid and backbone collections on node edit page.
   */
  // Drupal.behaviors.gridstackFieldView = {
  //   attach: function (context, settings) {
  //     const $window = $(window);
  //     const width = $window.width();
  //     const height = settings.gridstack_field.cellHeight ? settings.gridstack_field.cellHeight : 50;
  //     const verticalMargin = settings.gridstack_field.verticalMargin ? settings.gridstack_field.verticalMargin :  0;
  //     let items = [];
  //     let viewGridContent = $('.grid-stack');
  //     let nodes = viewGridContent.find('.grid-stack-item');
  //
  //     nodes.each(function() {
  //       const $this = $(this);
  //       const y = $this.attr('data-gs-y');
  //       const h = $this.attr('data-gs-height');
  //       const elementsHeight = h * height + verticalMargin * (h - 1);
  //       const top = y * height + verticalMargin * (y) + 2;
  //
  //       if ($this.css('display') != 'none') {
  //         $this.css('height', elementsHeight);
  //         $this.css('top', top);
  //         items.push($this);
  //       }
  //     });
  //
  //     let viewContentHeight = _getGridHeight(items) * (height + verticalMargin) - verticalMargin;
  //     viewGridContent.css('height', viewContentHeight);
  //   }
  // };

  $(document).ready(() => {
    let data = $('.field--name-field-json .field__item').text();
    if (data.length) {
      data = JSON.parse(data);
      let settingsKeys = Object.keys(data.settings);
      let settingsValues = Object.values(data.settings);

      const $window = $(window);
      const width = $window.width();
      const height = settingsValues.cellHeight ? settingsValues.cellHeight : 50;
      const verticalMargin = settingsValues.verticalMargin ? settingsValues.verticalMargin :  0;
      let items = [];
      let viewGridContent = $('.grid-stack');
      let nodes = viewGridContent.find('.grid-stack-item');

      nodes.each(function() {
        const $this = $(this);
        const y = $this.attr('data-gs-y');
        const h = $this.attr('data-gs-height');
        const elementsHeight = h * height + verticalMargin * (h - 1);
        const top = y * height + verticalMargin * (y) + 2;

        if ($this.css('display') != 'none') {
          $this.css('height', elementsHeight);
          $this.css('top', top);
          items.push($this);
        }
      });

      let viewContentHeight = _getGridHeight(items) * (height + verticalMargin) - verticalMargin;
      viewGridContent.css('height', viewContentHeight);
    }
  });

})(jQuery, Drupal, drupalSettings);
