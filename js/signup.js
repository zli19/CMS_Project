const form = document.getElementsByTagName("form")[0];
const pwdInput = document.getElementById("password");
const pwdInput2 = document.getElementById("password2");

form.addEventListener("submit", (e) => checkPasswordMatch(e));

const checkPasswordMatch = (e) => {
  e.preventDefault();
  if (pwdInput.value === pwdInput2.value) {
    form.submit();
  } else {
    const matchError = document.getElementById("match_error");
    matchError.classList.remove("hidden");
  }
};
