// Add functionality to the create button to toggle the createForm
let createBtn = document.getElementById("create");
let createForm = document.getElementById("createForm");

if (createBtn) {
  createBtn.addEventListener("click", () => {
    createForm.classList.toggle("hidden");
  });
}

// Toggle the innerHTML of the edit button between "Edit" and "Cancel Edit"
let buttons = document.getElementsByClassName("edit");
const onclick = (e) => {
  before = e.target.innerHTML;
  if (before.includes("Cancel")) {
    e.target.innerHTML = before.replace("Cancel ", "");
  } else {
    e.target.innerHTML = "Cancel " + before;
  }
};
if (buttons.length > 0) {
  for (let button of buttons) {
    button.addEventListener("click", onclick);
  }
}

// The method to toggle a review in display to a edit form back and forth.
const toggleForm = (id, type) => {
  // Target the review and toggle the display
  let info = document
    .getElementById(`${type}_${id}`)
    .querySelector(`.${type}_info`);
  info.classList.toggle("hidden");

  // Target the container to insert a edit form and toggle its display.
  let container = document
    .getElementById(`${type}_${id}`)
    .querySelector(`.${type}_form`);
  container.classList.toggle("hidden");

  // Check if the form has been inserted
  if (type !== "review" || container.innerHTML) return;

  if (type === "review") {
    // Clone the form skeleton from the existing createForm.
    const form = document.getElementById("form");
    let formClone = form.cloneNode(true);
    // Copy the review information into the form.
    let children = formClone.children;
    let star_rating = info.querySelector(".star_rating").innerHTML;
    let review_content = info.querySelector(".review_content").innerHTML;
    children[1].value = star_rating;
    children[2].value = review_content;
    children[5].name = "update";
    children[5].value = "update";

    // create a delete submit input
    const deleteBtn = document.createElement("input");
    deleteBtn.setAttribute("type", "submit");
    deleteBtn.setAttribute("name", "delete");
    deleteBtn.setAttribute("value", "delete");
    deleteBtn.setAttribute("class", "btn");
    deleteBtn.addEventListener("click", (e) => handleDelete(e, "review"));
    formClone.appendChild(deleteBtn);

    // Create a hidden input element to store the review_id.
    const reviewIdInput = document.createElement("input");
    reviewIdInput.setAttribute("type", "hidden");
    reviewIdInput.setAttribute("name", "review_id");
    reviewIdInput.setAttribute("value", id);
    formClone.appendChild(reviewIdInput);

    // Insert the cloned form into the target container.
    container.appendChild(formClone);
  } else {
    // type === "reply"
  }
};

const handleChange = () => {
  document.getElementById("sortForm").submit();
};

const handleDelete = (event, name) => {
  if (name === "review")
    message = "You are going to delete this review. Are you sure?";
  if (name === "room")
    message =
      "You are going to delete this room and its existing reviews. Are you sure?";
  result = confirm(message);
  if (!result) event.preventDefault();
};
