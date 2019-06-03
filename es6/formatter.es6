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
    let jsonData = $('.field--name-field-paragraphs-gridstack-json');

    if (jsonData.length) {
      jsonData.each(function () {
        let $data = $(this).text();
        let viewGridContent = $(this).parent().find('.grid-stack');
        gridSettings($data, viewGridContent);
      });
    }
  });

  /**
   * Set the settings to the paragraph.
   *
   * @param data
   *   Information about paragraph in json.
   * @param viewGridContent
   *   DOM presentation of paragraph.
   */
  function gridSettings(data, viewGridContent) {
    data = JSON.parse(data);
    let settingsValues = Object.keys(data.settings).map(function(key) {
      return data.settings[key]; });

    const height = settingsValues[0].cellHeight ? settingsValues[0].cellHeight : 50;
    const verticalMargin = settingsValues[0].verticalMargin ? settingsValues[0].verticalMargin :  0;
    let items = [];
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

})(jQuery, Drupal, drupalSettings);
