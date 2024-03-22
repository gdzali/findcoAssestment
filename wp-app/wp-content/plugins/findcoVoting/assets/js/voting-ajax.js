jQuery(document).ready(function ($) {
  $(".vote-button").click(function (e) {
    e.preventDefault();
    if ($(this).hasClass("disabled")) {
      return;
      
    }
    console.log($(this));
    var post_id = $(this).data("post-id");
    var vote_option = $(this).data("vote-option");
    var button = $(this);
    var data = {
      action: "handle_voting_ajax",
      post_id: post_id,
      vote_option: vote_option,
      security: voting_ajax_object.nonce,
    };
    $.post(voting_ajax_object.ajaxurl, data, function (response) {
      if (response.success) {
        var total_votes = response.yes_votes + response.no_votes;
        var yes_percentage =
          total_votes > 0
            ? Math.round((response.yes_votes / total_votes) * 100)
            : 0;
        var no_percentage =
          total_votes > 0
            ? Math.round((response.no_votes / total_votes) * 100)
            : 0;

        $("#yes-votes-" + post_id).text(yes_percentage + "%");
        $("#no-votes-" + post_id).text(no_percentage + "%");

        button.addClass("button-selected");
        $("section#voting .container span").html(
          "Thank you for your feedback."
        );
      } else {
        alert("Error: " + response.message);
      }
    });
  });
});
