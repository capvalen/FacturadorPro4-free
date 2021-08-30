/* Add here all your JS customizations */
$(function() {
  $(".switcher-hover").mouseenter(function() {
    $("#switcher-list").toggleClass("fade show active");
    $("#switcher-top").toggleClass("fade show active");
  });
  $(".switcher-hover").mouseleave(function() {
    $("#switcher-list").toggleClass("fade show active");
    $("#switcher-top").toggleClass("fade show active");
  });
});

$(function() {
	$("#styleSwitcherOpen").click(function() {
        $(".style-switcher").hasClass("active") ? $(".style-switcher").animate({
            right: "-" + $(".style-switcher").width() + "px"
        }, 300).removeClass("active") : $(".style-switcher").animate({
            right: "0"
        }, 300).addClass("active")
    });
});

