var data = ["14/01/2016", "16/02/2016", "16/03/2016", "16/04/2016", "16/05/2016", "16/06/2016", "16/07/2016", "16/08/2016", "16/09/2016", "16/10/2016", "16/11/2016", "16/12/2016"],
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

      //graph data must be taken  by timeline node
      var currentTimelineNode = $(this);
      //todo: get data with ajax call

      //1. regenerate current graph
      generateGraph(graphData, 'daily-graph-present');

      if ($(this).next().length) {
          generateGraph(graphData, 'daily-graph-future');
      } else {
          $('#daily-graph-future').fadeOut();
      }

      if ($(this).prev().length) {
          generateGraph(graphData, 'daily-graph-past');
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

        $.ajax({"url": "/get_events_from_date/04.09.2016"}).done(function (graphData) {
            console.log(graphData);
            // graphData = {
            //     nodes: [
            //         {data: {id:'54290', name:'I do stuff sunday'}},
            //         {data: {id:'54282', name:'Alex'}},
            //         {data: {id:'54288', name:'I do stuff with Adi saturday'}},
            //         {data: {id:'54292', name:'Day'}},
            //         {data: {id:'54286', name:'stuff'}},
            //         {data: {id:'54284', name:'Adi'}}
            //     ],
            //     edges: [
            //         {data: {id:'970', weight:'20', source:'54290', target:'54282'}},
            //         {data: {id:'966', weight:'20', source:'54288', target:'54282'}},
            //         {data: {id:'969', weight:'20', source:'54288', target:'54292'}},
            //         {data: {id:'967', weight:'20', source:'54282', target:'54286'}},
            //         {data: {id:'968', weight:'20', source:'54284', target:'54286'}},
            //         {data: {id:'971', weight:'20', source:'54282', target:'54286'}}
            //     ]
            // };

            var pastGraph = createGraph(graphData, 'daily-graph-past'),
                presentGraph = createGraph(graphData, 'daily-graph-present'),
                futureGraph = createGraph(graphData, 'daily-graph-future');

            timelineNodeHandler(presentGraph);
        });


        // var graphData;
        // $.getJSON("/get_events_from_date/04.09.2016", function (data) {
        //     console.log(data);
        //     graphData = data;
        // }).then(function () {
        //     console.log("the end is ");
        //     var pastGraph = createGraph(graphData, 'daily-graph-past'),
        //         presentGraph = createGraph(graphData, 'daily-graph-present'),
        //         futureGraph = createGraph(graphData, 'daily-graph-future');
        //
        //     timelineNodeHandler(presentGraph);
        // });

       /* ttpGraph.on('tap', 'node', {}, function(evt){
            var node = evt.cyTarget;
            console.log( 'tapped ' + node.id() );
        });*/
    });

    /*$.ajax({
        dataType: "json",
        url: "http://lc.test.stg1-dev.emag.network/get-data"
    });*/
});
