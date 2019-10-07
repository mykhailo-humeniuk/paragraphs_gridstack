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
    let data;
    let viewGridContent;
    let json_field = $('.field--name-field-paragraphs-gridstack-json');

    if (json_field.length === 1) {
      data = json_field.text();
      viewGridContent = json_field.closest('#site').find('.grid-stack');
    }
    else if (json_field.length > 1) {
      json_field.each(function () {
        data = $(this).text();
        viewGridContent = $(this).parent().find('.grid-stack');
      });
    }

    gridSettings(data, viewGridContent);
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
    const verticalMargin = settingsValues[0].verticalMargin ? settingsValues[0].verticalMargin : 0;
    const horizontalMargin = settingsValues[0].horizontal_margin ? settingsValues[0].horizontal_margin : 0;
    let items = [];
    let nodes = viewGridContent.find('.grid-stack-item');

    viewGridContent.css('grid-column-gap', horizontalMargin);
    viewGridContent.css('grid-row-gap', verticalMargin);

    const isIE = (window.navigator.userAgent.indexOf('MSIE ') > 0) || (window.navigator.userAgent.indexOf('Trident/') > 0);

    nodes.each(function() {
      const $this = $(this);
      const y = parseInt($this.attr('data-gs-y'));
      const h = $this.attr('data-gs-height');
      const positionY = (y + 1).toString();
      const elementsHeight = h * height + verticalMargin * (h - 1);
      const top = y * height + verticalMargin * (y) + 2;

      // if ($this.css('display') != 'none') {
      //   $this.css('height', elementsHeight);
      //   $this.css('top', top);
      //   items.push($this);
      // }
      if ($this.css('display') != 'none') {
        $this.css({
          'grid-row-start': positionY,
          'grid-row-end': 'span ' + h
        });
        items.push($this);
      }

      // Internet Explorer
      if (isIE) {
        $this.css({
          // Position Y.
          '-ms-grid-row': positionY,
          // Height.
          '-ms-grid-row-span': h,
          'height' : h * (height + verticalMargin)
        });
      }

    });

    if (!settingsValues[0].auto_height) {
      let viewContentHeight = _getGridHeight(items) * (height + verticalMargin) - verticalMargin;
      viewGridContent.css({
        'grid-auto-rows': height,
        'height': viewContentHeight,
      });
    }
    else {
      viewGridContent.css('grid-auto-rows', 'minmax(min-content, max-content)');
    }

    viewGridContent.css('opacity', 1);

  }

})(jQuery, Drupal, drupalSettings);
