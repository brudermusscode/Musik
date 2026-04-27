let __responder_close_timeout;
let __responder_close_timeout_time = 12000;

export class Responder {
  add(append, message, type = "success") {
    let $append = append;
    let $responder;
    let array;
    let current_responder = $append.querySelector("responder");

    if (current_responder && !current_responder.hasAttribute("deleted")) {
      clearTimeout(__responder_close_timeout);
      $responder = current_responder;

      return this.manipulate($responder, message, type);
    } else {
      $append.insertAdjacentHTML(
        "beforeend",
        `
        <responder ${type} fl alic rounded elevated>
          <div fl alic jucsb flexone gap>
            <div fl alic gap=smol+>
              <mi>info</mi>
              <p message text>${message}</p>
            </div>
          </div>
        </responder>
        `
      );

      array = {
        responder: null,
        append: $append,
      };

      $responder = $append.querySelector("responder:not([deleted])");

      setTimeout(() => {
        $responder.activate();
      }, 20);

      __responder_close_timeout = setTimeout(() => {
        this.close($responder);
      }, __responder_close_timeout_time);

      array.responder = $responder;
      return array;
    }
  }

  manipulate(responder, message, type) {
    let $responder = responder;

    $responder.deactivate();

    setTimeout(() => {
      $responder.removeAttribute("error");
      $responder.removeAttribute("success");
      $responder.removeAttribute("std");
      $responder.removeAttribute("feedback");
      $responder.removeAttribute("premium");
      $responder.setAttribute(type, true);
      $responder.querySelector("[message]").innerHTML = message;
      $responder.activate();
    }, 300);

    __responder_close_timeout = setTimeout(() => {
      this.close($responder);
    }, __responder_close_timeout_time);

    return true;
  }

  close(responder) {
    let $responder = responder,
      array;

    array = {
      responder: $responder,
    };

    array.responder.setAttribute("deleted", true);
    array.responder.deactivate();

    setTimeout(() => {
      array.responder.remove();
    }, 400);

    return array;
  }
}
