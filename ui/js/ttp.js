var data = ["14/01/2016", "16/02/2016", "16/03/2016", "16/04/2016", "16/05/2016", "16/06/2016", "16/07/2016", "16/08/2016", "16/09/2016", "16/10/2016", "16/11/2016", "16/12/2016"],
    events = $(".events").find("ol"),
    eventsContent = $(".events-content").find("ol"),
    selectedClass = 'class="selected"';

var getTimelineData = function () { //input data will be passed as param
    $.each(data, function (index, event) {
        events.append('<li id="' + event + '"><a href="#0" data-date="' + event + '" ' + selectedClass + '>' + event + '</a></li>');
        eventsContent.append('<li data-date="' + event + '"' + selectedClass + '>Lorem ' + event + '</li>');
        selectedClass = "";
    });

    return $('#ttp-events');
};

var timelineNodeHandler = function (graph) {
  events.find("li").on("click", function (e) {
      $("#daily-graph").fadeOut().remove();
      $("#graph-wrapper").html('<div id="daily-graph"></div>');

      var graphData = {
          nodes: [
              { data: { id: 'a' } },
              { data: { id: 'b' } },
              { data: { id: 'c' } },
              { data: { id: 'd' } },
              { data: { id: 'e' } }
          ],

          edges: [
              { data: { id: 'a"e', weight: 1, source: 'a', target: 'e' } },
              { data: { id: 'ab', weight: 3, source: 'a', target: 'b' } },
              { data: { id: 'be', weight: 4, source: 'b', target: 'e' } },
              { data: { id: 'bc', weight: 5, source: 'e', target: 'c' } },
              { data: { id: 'ce', weight: 10, source: 'a', target: 'e' } },
              { data: { id: 'cd', weight: 2, source: 'c', target: 'd' } },
              { data: { id: 'de', weight: 7, source: 'd', target: 'b' } },
              { data: { id: 'de', weight: 7, source: 'b', target: 'bd' } }
          ]
      };
      createGraph(graphData);
  });
};

var createGraph = function (graphData) {
    var cy = cytoscape({
        container: document.getElementById('daily-graph'),

        boxSelectionEnabled: false,
        autounselectify: true,

        style: cytoscape.stylesheet()
            .selector('node')
            .css({
                'content': 'data(id)'
            })
            .selector('edge')
            .css({
                'target-arrow-shape': 'triangle',
                'width': 4,
                'line-color': '#ddd',
                'target-arrow-color': '#ddd',
                'curve-style': 'bezier'
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
            name: 'breadthfirst',
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
        var graphData = {
            nodes: [
                { data: { id: 'a' } },
                { data: { id: 'b' } },
                { data: { id: 'c' } },
                { data: { id: 'd' } },
                { data: { id: 'e' } }
            ],

            edges: [
                { data: { id: 'a"e', weight: 1, source: 'a', target: 'e' } },
                { data: { id: 'ab', weight: 3, source: 'a', target: 'b' } },
                { data: { id: 'be', weight: 4, source: 'b', target: 'e' } },
                { data: { id: 'bc', weight: 5, source: 'b', target: 'c' } },
                { data: { id: 'ce', weight: 6, source: 'c', target: 'e' } },
                { data: { id: 'cd', weight: 2, source: 'c', target: 'd' } },
                { data: { id: 'de', weight: 7, source: 'd', target: 'e' } }
            ]
        };
        var ttpGraph = createGraph(graphData);
        timelineNodeHandler(ttpGraph);
    });
});
