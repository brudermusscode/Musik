import * as Frontend from "../framework/frontend";

$(function () {
  $(document).on("click", "[data-action='library:reorder']", function (e) {
    let library = this.closest("library");

    if (library.hasAttribute("editing")) library.removeAttribute("editing");
    else library.setAttribute("editing", true);
  });

  $(document).on(
    "click",
    "library-entry [move-up], library-entry [move-down]",
    function (e) {
      let library = this.closest("library");
      let entry = this.closest("library-entry");

      if (this.hasAttribute("move-up") && entry.previousElementSibling) {
        entry.parentNode.insertBefore(entry, entry.previousElementSibling);
      } else if (this.hasAttribute("move-down") && entry.nextElementSibling) {
        entry.nextElementSibling.after(entry);
      }
    },
  );

  $(document).on("submit", "[data-form='library:reorder']", function (e) {
    e.preventDefault();

    let form = this;
    let formdata = new FormData(this);
    let library = this.find("library");

    $.ajax({
      url: "/bookmark/update",
      method: "POST",
      data: formdata,
      success: function (data) {
        console.log(data);
        if (data.status) {
          library.removeAttribute("editing");

          Frontend.ajax_response("success");
        }
      },
    });
  });
});
