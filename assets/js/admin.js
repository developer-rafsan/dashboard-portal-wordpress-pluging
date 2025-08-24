// Get button elements
const caseActiveBtnAdmin = document.getElementById("active-cases-btn-admin");
const caseRequestedBtnAdmin = document.getElementById("requested-cases-btn-admin");
const caseUnpadBtnAdmin = document.getElementById("unpaid-cases-btn-admin");
const caseComplitedBtnAdmin = document.getElementById("complited-cases-btn-admin");

// Content sections
const activeContent = document.getElementById("active-case-content-admin");
const requestedContent = document.getElementById("requested-case-content-admin");
const unpaidContent = document.getElementById("unpaid-case-content-admin");
const completedContent = document.getElementById("completed-case-content-admin");


// Function to handle the active state toggle
function setActiveButton(activeBtn, ...otherBtns) {
  activeBtn.classList.add("bg-blue-500", "text-white");
  activeBtn.classList.remove("text-gray-600");

  otherBtns.forEach(btn => {
    btn.classList.remove("bg-blue-500", "text-white");
    btn.classList.add("text-gray-600");
  });
}

// Function to toggle content visibility
function toggleContent(activeContent, inactiveContents) {
  activeContent.classList.remove("hidden");
  inactiveContents.forEach(content => content.classList.add("hidden"));
}

// Event Listeners
caseActiveBtnAdmin.addEventListener("click", function () {
  toggleContent(activeContent, [requestedContent, unpaidContent, completedContent]);
  setActiveButton(caseActiveBtnAdmin, caseRequestedBtnAdmin, caseUnpadBtnAdmin, caseComplitedBtnAdmin);
});

caseRequestedBtnAdmin.addEventListener("click", function () {
  toggleContent(requestedContent, [activeContent, unpaidContent, completedContent]);
  setActiveButton(caseRequestedBtnAdmin, caseActiveBtnAdmin, caseUnpadBtnAdmin, caseComplitedBtnAdmin);
});

caseUnpadBtnAdmin.addEventListener("click", function () {
  toggleContent(unpaidContent, [activeContent, requestedContent, completedContent]);
  setActiveButton(caseUnpadBtnAdmin, caseActiveBtnAdmin, caseRequestedBtnAdmin, caseComplitedBtnAdmin);
});

caseComplitedBtnAdmin.addEventListener("click", function () {
  toggleContent(completedContent, [activeContent, requestedContent, unpaidContent]);
  setActiveButton(caseComplitedBtnAdmin, caseActiveBtnAdmin, caseRequestedBtnAdmin, caseUnpadBtnAdmin);
});

// Set initial active state
setActiveButton(caseActiveBtnAdmin, caseRequestedBtnAdmin, caseUnpadBtnAdmin, caseComplitedBtnAdmin);
toggleContent(activeContent, [requestedContent, unpaidContent, completedContent]);
