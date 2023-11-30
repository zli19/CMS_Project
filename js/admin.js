// Add functionality to the create button to toggle the createForm
let createBtn = document.getElementById("create");
let createTable = document.getElementById("createTable");

if (createBtn) {
  createBtn.addEventListener("click", (e) => {
    createTable.classList.toggle("hidden");
  });
}

// Toggle the innerHTML of the edit button between "Edit" and "Cancel"
let buttons = document.getElementsByClassName("edit");
let username = document.getElementById("username");
let email = document.getElementById("email");
let discriminator = document.getElementById("discriminator");
const handleClick = (e) => {
  let b = e.target;

  let usernameTr = b.parentNode.nextElementSibling.nextElementSibling;
  let emailTr = usernameTr.nextElementSibling;
  let roleTr = emailTr.nextElementSibling;
  let usernameContent = usernameTr.innerHTML;
  let emailContent = emailTr.innerHTML;
  let roleContent = roleTr.innerHTML;

  let tr = b.parentNode.parentNode;
  let tbody = tr.parentNode;
  console.log(tbody);
  if (tbody.childElementCount > 2) {
    for (let i = 1; i < tbody.childElementCount; i++) {
      tbody.children[i].classList.toggle("hidden");
    }
  } else {
    let siblingTr = tr.cloneNode(true);
    let cancelBtn = siblingTr.children[0].children[0];
    cancelBtn.classList.remove("btn-secondary");
    cancelBtn.classList.add("btn");
    cancelBtn.value = "cancel";
    cancelBtn.addEventListener("click", handleClick);

    let updateBtn = siblingTr.children[1].children[0];
    updateBtn.name = "update";
    updateBtn.value = "update";

    let usernameInput = username.cloneNode();
    usernameInput.removeAttribute("id");
    usernameInput.removeAttribute("value");
    usernameInput.setAttribute("value", usernameContent);
    let usernameTd = siblingTr.children[2];
    usernameTd.innerHTML = "";
    usernameTd.appendChild(usernameInput);

    let emailInput = email.cloneNode();
    emailInput.removeAttribute("id");
    emailInput.removeAttribute("value");
    emailInput.setAttribute("value", emailContent);
    let emailTd = siblingTr.children[3];
    emailTd.innerHTML = "";
    emailTd.appendChild(emailInput);

    let roleSelect = discriminator.cloneNode(true);
    roleSelect.removeAttribute("id");
    roleSelect.value = roleContent === "Administrator" ? "admin" : "customer";
    let roleTd = siblingTr.children[4];
    roleTd.innerHTML = "";
    roleTd.appendChild(roleSelect);

    tbody.appendChild(siblingTr);
    tr.classList.add("hidden");
  }
};

if (buttons.length > 0) {
  for (let button of buttons) {
    button.addEventListener("click", handleClick);
  }
}

// Check if password match
const form = document.getElementById("createForm");
const pwdInput = document.getElementById("password");
const pwdInput2 = document.getElementById("password2");

form.addEventListener("submit", (e) => {
  e.preventDefault();
  if (pwdInput.value === pwdInput2.value) {
    form.submit();
  } else {
    const matchError = document.getElementById("match_error");
    matchError.classList.remove("hidden");
  }
});
