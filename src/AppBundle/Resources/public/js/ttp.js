function formatDateString(dateParts, delimiter) {
    delimiter = typeof delimiter !== 'undefined' ? delimiter : '/';
    var stringDate = dateParts[0] + delimiter + dateParts[1] + delimiter + dateParts[2];
    return stringDate;
}
function getDateFormat(cDate, delimiter) {
    var dateParts = [];
    dateParts[0] = cDate.getDate();
    dateParts[1] = cDate.getMonth() + 1; //January is 0!
    dateParts[2] = cDate.getFullYear();
    if (dateParts[0] < 10) {
        dateParts[0] = '0' + dateParts[0]
    }
    if (dateParts[1] < 10) {
        dateParts[1] = '0' + dateParts[1]
    }

    return formatDateString(dateParts, delimiter);
}
var today = new Date();
//var tomorrow = new Date();
//tomorrow.setDate(today.getDate() + 1);
//var yesterday = new Date();
//yesterday.setDate(today.getDate() + 1);

var data,
    events = $(".events").find("ol"),
    eventsContent = $(".events-content").find("ol"),
    selectedClass = 'class="selected"',
    eventList = $("#event_list");
    timelineDataRoute = '',
    timelineNodeDataRoute = '';

var getTimelineData = function () { //input data will be passed as param
    $.each(data, function (index, event) {
        if (event == getDateFormat(today)) {
            var selectedClassNow = selectedClass
        }
        dateParts = event.split("/");
        events.append('<li id="' + event + '"><a href="#0" data-date="' + event + '" ' + selectedClassNow + '>' + dateParts[0]+'</br>' + dateParts[1]+ '</br>' + dateParts[2]+ '</a></li>');
        eventsContent.append('<li data-date="' + event + '"' + selectedClass + '>Lorem ' + event + '</li>');
        selectedClassNow = "";
    });

    return $('#ttp-events');
};

var geteventListData = function (data) {
    $.each(data, function (index, event) {
        eventList.append('<li id="' + event.id + '"><a href="#0" data-date="' + event.date + '">' + event.name + '</a></li>');
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
    $.getJSON("/get_events_with_date", function(json){
        data = json;
    }).then(function () {
        geteventListData(data);
    });

    $.getJSON("/count_events_from_date", function(json){
        data = json;
    }).then(function () {
        getTimelineData().promise().done(function () {
            initTimeline($(this));
            var presentDate = getDateFormat(today);
            for (i = 0; i < data.length; i++) {
                if (data[i] == presentDate) {
                    if (i == 0) {
                        var left_neighbour = '';
                    } else {
                        var left_neighbour = data[i - 1];
                    }
                    if (i == data.length) {
                        var right_neighbour = '';
                    } else {
                        var right_neighbour = data[i + 1];
                    }
                }
            }

            var graphData;
            $.getJSON("/get_events_from_date/"+getDateFormat(today,'.'), function (data) {
                graphData = data;
            }).then(function () {
                var //pastGraph = createGraph(graphData, 'daily-graph-past'),
                    presentGraph = createGraph(graphData, 'daily-graph-present');
                //futureGraph = createGraph(graphData, 'daily-graph-future');

                timelineNodeHandler(presentGraph);
            });
            if (left_neighbour !== '') {
                var dateParts = left_neighbour.split("/");
                $.getJSON("/get_events_from_date/" + formatDateString(dateParts, '.'), function (data) {
                    graphData = data;
                }).then(function () {
                    var presentGraph = createGraph(graphData, 'daily-graph-past');
                });
            }
            if (right_neighbour !== '') {
                $.getJSON("/get_events_from_date/" + formatDateString(right_neighbour.split("/"), '.'), function (data) {
                    graphData = data;
                }).then(function () {
                    var presentGraph = createGraph(graphData, 'daily-graph-future');
                });
            }
        });
    });

});
