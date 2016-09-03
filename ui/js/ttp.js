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

var timelineNodeHandler = function () {
  events.find("li").on("click", function (e) {
      console.log("you clicked " + $(this).attr("id"));
      //todo: using this date get a json with the assocgraph
  });
};

jQuery(document).ready(function ($) {
    getTimelineData().promise().done(function () {
        initTimeline($(this));
        timelineNodeHandler();
    });
});