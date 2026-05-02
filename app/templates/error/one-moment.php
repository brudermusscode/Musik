<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/x-icon" href="/favicon.svg" />
  <meta http-equiv="refresh" content="4">

  <style>
    .spinner {
      width: 42px;
      height: 42px;
      --clr: #40f65e;
      --clr-alpha: rgb(247, 197, 159, 0.1);
      animation: spinner 1.6s infinite ease;
      transform-style: preserve-3d;
    }

    .spinner>div {
      background-color: var(--clr-alpha);
      height: 100%;
      position: absolute;
      width: 100%;
      border: 3.5px solid var(--clr);
    }

    .spinner div:nth-of-type(1) {
      transform: translateZ(-20px) rotateY(180deg);
    }

    .spinner div:nth-of-type(2) {
      transform: rotateY(-270deg) translateX(50%);
      transform-origin: top right;
    }

    .spinner div:nth-of-type(3) {
      transform: rotateY(270deg) translateX(-50%);
      transform-origin: center left;
    }

    .spinner div:nth-of-type(4) {
      transform: rotateX(90deg) translateY(-50%);
      transform-origin: top center;
    }

    .spinner div:nth-of-type(5) {
      transform: rotateX(-90deg) translateY(50%);
      transform-origin: bottom center;
    }

    .spinner div:nth-of-type(6) {
      transform: translateZ(20px);
    }

    @keyframes spinner {
      0% {
        transform: rotate(45deg) rotateX(-25deg) rotateY(25deg);
      }

      50% {
        transform: rotate(45deg) rotateX(-385deg) rotateY(25deg);
      }

      100% {
        transform: rotate(45deg) rotateX(-385deg) rotateY(385deg);
      }
    }

    html {
      font-size: 100%;
    }

    * {
      margin: 0;
      padding: 0;
    }

    body {
      min-height: 100svh;
      color: #fff;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 1.2em;
      line-height: 1.2;
      letter-spacing: -0.01em;
      background-color: transparent;
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;
    }

    background-blur {
      z-index: -10;
      display: block;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      backdrop-filter: blur(42px);
      background: rgba(0, 0, 0, .62);
    }

    [fl] {
      display: flex;
    }

    [jucc] {
      justify-content: center;
    }

    [alic] {
      align-items: center;
    }

    [fldircol] {
      flex-direction: column;
    }

    [window] {
      background: rgba(36, 36, 36, 0.62);
      backdrop-filter: blur(18px);
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      border-radius: 32px;
    }
  </style>

  <title>Musik, Bruder!</title>
</head>

<body style="background-image: url(/assets/images/colors.svg);">

  <background-blur></background-blur>

  <div fl alic jucc fldircol style="gap:1.8em;position:fixed;top:50%;left:50%;translate:-50% -50%;">
    <div style="margin-bottom:24px;">
      <div class="spinner">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>

    <div fl fldircol alic jucc>
      <p style="font-size:42px;font-weight:600;">Einen Moment</p>
      <p>
        Ein paar Sekunden braucht der Bruder noch.
      </p>
    </div>

    <div window style="padding:2px;padding-right:24px;gap:.8em;" fl alic>
      <div style="border-radius:28px;background:rgba(255,255,255,.06);height:4.2em;width:4.2em;">

      </div>

      <div fl fldircol style="gap:.4em;">
        <span style="display:block;width:182px;height:14px;background:rgba(255,255,255,.04);border-radius:12px;" rounded></span>
        <span style="display:block;width:60px;height:6px;background:rgba(255,255,255,.04);border-radius:12px;" rounded></span>
      </div>

      <div fl alic style="gap:.2em;margin-left:182px;">
        <div style="height:56px;width:56px;background: rgba(255,255,255,.04);border-radius:50%;"></div>
      </div>
    </div>
  </div>
</body>