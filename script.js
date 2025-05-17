function validateForm() {
  const user = document.getElementById("username").value.trim();
  const pass = document.getElementById("password").value.trim();

  if (!user || !pass) {
    document.getElementById("error-message").innerText = "All fields are required.";
    return false;
  }

  return true;
}
