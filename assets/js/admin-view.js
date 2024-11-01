document.addEventListener("DOMContentLoaded", function() {
  const hoverableSpans = document.querySelectorAll(".zest_suggestion_div span");

  hoverableSpans.forEach(function(hoverableSpan) {
    const hiddenParagraph = hoverableSpan.nextElementSibling;

    hoverableSpan.addEventListener("mouseover", function() {
      hiddenParagraph.style.display = "block";
    });

    hoverableSpan.addEventListener("mouseout", function() {
      hiddenParagraph.style.display = "none";
    });
  });
});

