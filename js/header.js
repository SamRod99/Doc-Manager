document.addEventListener("DOMContentLoaded", () => {
    const title = document.body.getAttribute("data-title") || "Dashboard Principal";
    const header = `
    <header>
      <h1 id="page-title">${title}</h1>

      <div class="topbar-actions">

      </div>
    </header>
  `;
  document.getElementById("header-container").innerHTML = header;
});