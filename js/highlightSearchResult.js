let searchValue = document.getElementById("searchInput").value.trim();
if (searchValue) {
  let userNames = document.getElementsByClassName("user_name");
  let roomNames = document.getElementsByClassName("room_name");
  let reviewContent = document.getElementsByClassName("review_content");

  for (let userName of userNames) {
    let content = userName.innerHTML;
    let highlightedContent = content.replace(
      new RegExp(searchValue, "gi"),
      '<span class="bg-gray-300">$&</span>'
    );
    userName.innerHTML = highlightedContent;
  }

  for (let roomName of roomNames) {
    let content = roomName.innerHTML;
    let highlightedContent = content.replace(
      new RegExp(searchValue, "gi"),
      '<span class="bg-gray-300">$&</span>'
    );
    roomName.innerHTML = highlightedContent;
  }

  for (let review of reviewContent) {
    let content = review.innerHTML;
    let highlightedContent = content.replace(
      new RegExp(searchValue, "gi"),
      '<span class="bg-gray-300">$&</span>'
    );
    review.innerHTML = highlightedContent;
  }
}
