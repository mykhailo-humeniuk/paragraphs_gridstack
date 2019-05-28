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
  $(document).ready(() => {
    let data = $('.field--name-field-paragraphs-gridstack-json').text();
    if (data.length) {
      data = JSON.parse(data);
      let settingsKeys = Object.keys(data.settings);
      let settingsValues = Object.keys(data.settings).map(function(key) { return data.settings[key]; });

      const $window = $(window);
      const width = $window.width();
      const height = settingsValues[0].cellHeight ? settingsValues[0].cellHeight : 50;
      const verticalMargin = settingsValues[0].verticalMargin ? settingsValues[0].verticalMargin :  0;
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
