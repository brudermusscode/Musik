export const serialize_cookie_domain = () => {
  const domain = window.location.hostname;
  const cleanDomain = domain.replace(/^www\./i, "");

  return cleanDomain;
};

export const set = (name, value, expiration_in_days) => {
  let date = new Date();
  let domain = serialize_cookie_domain();

  date.setTime(date.getTime() + expiration_in_days * 24 * 60 * 60 * 1000);

  const expires = "expires=" + date.toUTCString();

  document.cookie = `${name}=${value}; ${expires}; path=/; domain=${domain}; Secure=true; SameSite=Lax`;
};

export const get = (cname) => {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(";");

  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];

    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }

    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }

  return null;
};

export const remove = (cookieName) => {
  document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
};
