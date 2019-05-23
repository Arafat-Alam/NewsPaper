$(function () {

    /*Slick Nav Js*/
    $('#menu').slicknav();


    /*smarticker1*/
    $('.smarticker1').smarticker();


    $("#news3").owlCarousel({
        items: 3,
        pagination: true,
        autoPlay: 5000,
        stopOnHover: true,
        responsive: {
            0: {
                items: 2
            },
            768: {
                items: 2
            },
            1000: {
                items: 2
            }
        }
    });




    $("#news14").owlCarousel({
        items: 4,
        pagination: true,
        autoPlay: true,
        stopOnHover: true
    });

});



/*Social Share Button*/
$("#social-share").jsSocials({
    url: "http://www.google.com",
    text: "Google Search Page",
    showCount: true,
    showLabel: false,
    shares: [
//        {share: "twitter", via: "artem_tabalin", hashtags: "search,google"},
        "facebook",
        "twitter",
        "googleplus",
        "linkedin",
        "pinterest"
    ]
});