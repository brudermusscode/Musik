Element.prototype.append_loader = function (with_overlay = false) {
  let loaderHTML = `
      <div class="spinner">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>`;

  if (with_overlay)
    loaderHTML =
      `<div animation=fade-in background=hover-dark posabs
        style="z-index:20;height:100%;width:100%;top:0;left:0;backdrop-filter:blur(100px);" fl alic jucc>` +
      loaderHTML +
      `</div>`;
  else
    loaderHTML =
      `<div animation=fade-in fl alic jucc pblock100 w100>` +
      loaderHTML +
      `</div>`;

  return this.insertAdjacentHTML("afterbegin", loaderHTML);
};

Element.prototype.loading = function () {
  return this.hasAttribute("loading");
};

Element.prototype.load = function () {
  return this.setAttribute("loading", true);
};

Element.prototype.unload = function () {
  return this.removeAttribute("loading");
};
