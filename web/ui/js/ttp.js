var data = ["05/09/2016", "04/09/2016", "03/09/2016", "25/09/2016", "01/09/2016"],
    events = $(".events").find("ol"),
    eventsContent = $(".events-content").find("ol"),
    selectedClass = 'class="selected"',
    timelineDataRoute = '',
    timelineNodeDataRoute = '';

var getTimelineData = function () { //input data will be passed as param
    $.each(data, function (index, event) {
        events.append('<li id="' + event + '"><a href="#0" data-date="' + event + '" ' + selectedClass + '>' + event + '</a></li>');
        eventsContent.append('<li data-date="' + event + '"' + selectedClass + '>Lorem ' + event + '</li>');
        selectedClass = "";
    });

    return $('#ttp-events');
};

var generateGraph = function (data, selector) {
    var presentGraphItem = $('#' + selector),
        presentGraphParent = presentGraphItem.parent();

    presentGraphItem.fadeOut().remove();
    presentGraphParent.html('<div id="' + selector + '"></div>');
    createGraph(data, selector);
};

var timelineNodeHandler = function (graph) {
  events.find("li").on("click", function (e) {
      var graphData;

      //graph data must be taken  by timeline node
      var currentTimelineNode = $(this),
          currentDate = currentTimelineNode.attr('id');

      currentDate = currentDate.replace("/", ".");
      currentDate = currentDate.replace("/", ".");

      $.getJSON("/get_events_from_date/" + currentDate, function (data) {
          graphData = data;
      }).then(function () {
          generateGraph(graphData, 'daily-graph-present');
      });

      if ($(this).next().length) {
          currentDate = $(this).next().attr('id');
          currentDate = currentDate.replace("/", ".");
          currentDate = currentDate.replace("/", ".");
          $.getJSON("/get_events_from_date/" + currentDate, function (data) {
              graphData = data;
          }).then(function () {
              generateGraph(graphData, 'daily-graph-future');
          });
      } else {
          $('#daily-graph-future').fadeOut();
      }

      if ($(this).prev().length) {
          currentDate = $(this).prev().attr('id');
          currentDate = currentDate.replace("/", ".");
          currentDate = currentDate.replace("/", ".");

          $.getJSON("/get_events_from_date/" + currentDate, function (data) {
              graphData = data;
          }).then(function () {
              generateGraph(graphData, 'daily-graph-past');
          });
      } else {
          $('#daily-graph-past').fadeOut();
      }
  });
};

var createGraph = function (graphData, idSelector) {
    var cy = cytoscape({
        container: document.getElementById(idSelector),

        boxSelectionEnabled: false,
        autounselectify: true,

        style: cytoscape.stylesheet()
            .selector('node')
            .css({
                'content': 'data(name)'
            })
            .selector('edge')
            .css({
                'target-arrow-shape': 'triangle',
                'width': 4,
                'line-color': '#ddd',
                'target-arrow-color': '#ddd',
                'curve-style': 'bezier',
                "content": 'data(label)'
            })
            .selector('.highlighted')
            .css({
                'background-color': '#61bffc',
                'line-color': '#61bffc',
                'target-arrow-color': '#61bffc',
                'transition-property': 'background-color, line-color, target-arrow-color',
                'transition-duration': '0.5s'
            }),

        elements: graphData,

        layout: {
            name: 'cose',
            directed: true,
            roots: '#a',
            padding: 10
        }
    });

    var bfs = cy.elements().bfs('#a', function(){}, true);

    var i = 0;
    var highlightNextEle = function(){
        if( i < bfs.path.length ){
            bfs.path[i].addClass('highlighted');

            i++;
            setTimeout(highlightNextEle, 1000);
        }
    };

// kick off first highlight
    highlightNextEle();

    return cy;
};

jQuery(document).ready(function ($) {
    getTimelineData().promise().done(function () {
        initTimeline($(this));

        var graphData;
        $.getJSON("/get_events_from_date/04.09.2016", function (data) {
            graphData = data;
        }).then(function () {
            var pastGraph = createGraph(graphData, 'daily-graph-past'),
                presentGraph = createGraph(graphData, 'daily-graph-present'),
                futureGraph = createGraph(graphData, 'daily-graph-future');

            timelineNodeHandler(presentGraph);
        });
    });
});
