/**
 * CONTTA V2.0 - Dark Mode Toggle
 */

// Inicializar tema ao carregar a página
function initTheme() {
  const savedTheme = localStorage.getItem("contta-theme") || "light";
  document.documentElement.setAttribute("data-theme", savedTheme);
  updateThemeIcon(savedTheme);
}

// Alternar entre light e dark
function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute("data-theme");
  const newTheme = currentTheme === "light" ? "dark" : "light";
  
  document.documentElement.setAttribute("data-theme", newTheme);
  localStorage.setItem("contta-theme", newTheme);
  updateThemeIcon(newTheme);
}

// Atualizar ícone do botão
function updateThemeIcon(theme) {
  const themeBtn = document.getElementById("theme-toggle");
  if (themeBtn) {
    themeBtn.textContent = theme === "light" ? "🌙" : "☀️";
    themeBtn.setAttribute("aria-label", theme === "light" ? "Ativar modo escuro" : "Ativar modo claro");
  }
}

// Exportar funções
export { initTheme, toggleTheme };

