const setCookie = (cookieName, cookieValue, expirationDays) => {
  const d = new Date();
  d.setTime(d.getTime() + expirationDays * 24 * 60 * 60 * 1000);
  const expires = `expires=${d.toUTCString()}`;
  document.cookie = `${cookieName}=${cookieValue}; ${expires}; path=/`;
};

const readCookie = (cookieName) => {
  const nameEQ = `${cookieName}=`;
  const cookies = document.cookie.split(";");
  for (let cookie of cookies) {
    cookie = cookie.trim();
    if (cookie.startsWith(nameEQ)) {
      return cookie.substring(nameEQ.length);
    }
  }
  return null;
};

const updateAllCheckboxes = (checkboxes, isChecked) => {
  checkboxes.forEach((checkbox) => {
    checkbox.checked = isChecked;
    setCookie(checkbox.id, isChecked ? "true" : "false", 180);
  });
};

const setupSelectAll = (selectAllId, checkboxClass) => {
  const selectAll = document.getElementById(selectAllId);
  const checkboxes = document.querySelectorAll(`.${checkboxClass}`);

  selectAll.addEventListener("change", function () {
    updateAllCheckboxes(checkboxes, this.checked);
  });

  let allChecked = true;
  checkboxes.forEach((checkbox) => {
    const cookieValue = readCookie(checkbox.id);
    if (cookieValue === "false") {
      allChecked = false;
    }
  });
  selectAll.checked = allChecked;
};

document.addEventListener("DOMContentLoaded", () => {
  // راه‌اندازی انتخاب همه برای هر بخش
  setupSelectAll("selectAllTypes", "type-checkbox");
  setupSelectAll("selectAllStatuses", "status-checkbox");

  // مدیریت چک‌باکس‌های تکی
  const checkboxes = document.querySelectorAll(
    '.form-check-input:not([id^="selectAll"])'
  );
  checkboxes.forEach((checkbox) => {
    // رویداد تغییر برای چک‌باکس‌های تکی
    checkbox.addEventListener("change", function () {
      setCookie(this.id, this.checked ? "true" : "false", 180);

      // بررسی وضعیت انتخاب همه
      const sectionCheckboxes = document.querySelectorAll(
        `.${this.className.replace("form-check-input", "").trim()}`
      );
      const selectAllId = `selectAll${
        this.className
          .replace("-checkbox", "")
          .replace("form-check-input", "")
          .trim()
          .charAt(0)
          .toUpperCase() +
        this.className
          .replace("-checkbox", "")
          .replace("form-check-input", "")
          .trim()
          .slice(1)
      }`;
      const selectAll = document.getElementById(selectAllId);

      if (selectAll) {
        let allChecked = true;
        sectionCheckboxes.forEach((cb) => {
          if (!cb.checked) allChecked = false;
        });
        selectAll.checked = allChecked;
      }
    });

    // مقداردهی اولیه از کوکی‌ها
    const cookieValue = readCookie(checkbox.id);
    if (cookieValue === null) {
      checkbox.checked = true;
      setCookie(checkbox.id, "true", 180);
    } else {
      checkbox.checked = cookieValue === "true";
    }
  });

  // بارگذاری مجدد صفحه هنگام بسته شدن مدال
  const themeSettingsOffcanvas = document.getElementById(
    "theme-settings-offcanvas"
  );
  themeSettingsOffcanvas.addEventListener("hidden.bs.offcanvas", () => {
    location.reload();
  });
});
