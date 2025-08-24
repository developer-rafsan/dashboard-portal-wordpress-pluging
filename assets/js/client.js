// Get button elements
const caseActiveBtn = document.getElementById("active-cases-btn");
const requestedCasesBtn = document.getElementById("requested-cases-btn");
const completedCasesBtn = document.getElementById("completed-cases-btn");
const rejectedCasesBtn = document.getElementById("rejected-cases-btn");
const profileBtn = document.getElementById("clients-profile-btn");

// Function to handle the active state toggle
function setActiveButton(
  activeBtn,
  inactiveBtn,
  inactiveBtn2,
  inactiveBtn3,
  inactiveBtn4
) {
  // Add active class to the clicked button
  activeBtn.classList.add("bg-blue-500", "text-white");
  activeBtn.classList.remove("text-gray-600");

  // Remove active class from the other buttons
  inactiveBtn.classList.remove("bg-blue-500", "text-white");
  inactiveBtn.classList.add("text-gray-600");

  inactiveBtn2.classList.remove("bg-blue-500", "text-white");
  inactiveBtn2.classList.add("text-gray-600");

  inactiveBtn3.classList.remove("bg-blue-500", "text-white");
  inactiveBtn3.classList.add("text-gray-600");

  inactiveBtn4.classList.remove("bg-blue-500", "text-white");
  inactiveBtn4.classList.add("text-gray-600");
}

// Function to toggle content visibility
function toggleContent(activeContent, inactiveContents) {
  // Show the active content
  activeContent.classList.remove("hidden");

  // Hide all other content
  inactiveContents.forEach((content) => content.classList.add("hidden"));
}

// Add click event to Case Overview button
caseActiveBtn.addEventListener("click", function () {
  toggleContent(document.getElementById("active-cases-content"), [
    document.getElementById("requested-cases-content"),
    document.getElementById("completed-cases-content"),
    document.getElementById("rejected-cases-content"),
    document.getElementById("profile-content"),
  ]);

  // Set active button style
  setActiveButton(
    caseActiveBtn,
    requestedCasesBtn,
    completedCasesBtn,
    rejectedCasesBtn,
    profileBtn
  );
});

// Add click event to New Request button
requestedCasesBtn.addEventListener("click", function () {
  toggleContent(document.getElementById("requested-cases-content"), [
    document.getElementById("active-cases-content"),
    document.getElementById("completed-cases-content"),
    document.getElementById("rejected-cases-content"),
    document.getElementById("profile-content"),
  ]);

  // Set active button style
  setActiveButton(
    requestedCasesBtn,
    caseActiveBtn,
    completedCasesBtn,
    rejectedCasesBtn,
    profileBtn
  );
});

// Add click event to Messages button
completedCasesBtn.addEventListener("click", function () {
  toggleContent(document.getElementById("completed-cases-content"), [
    document.getElementById("active-cases-content"),
    document.getElementById("requested-cases-content"),
    document.getElementById("rejected-cases-content"),
    document.getElementById("profile-content"),
  ]);

  // Set active button style
  setActiveButton(
    completedCasesBtn,
    caseActiveBtn,
    requestedCasesBtn,
    rejectedCasesBtn,
    profileBtn
  );
});

// Add click event to Documents button
rejectedCasesBtn.addEventListener("click", function () {
  toggleContent(document.getElementById("rejected-cases-content"), [
    document.getElementById("active-cases-content"),
    document.getElementById("requested-cases-content"),
    document.getElementById("completed-cases-content"),
    document.getElementById("profile-content"),
  ]);

  // Set active button style
  setActiveButton(
    rejectedCasesBtn,
    caseActiveBtn,
    requestedCasesBtn,
    completedCasesBtn,
    profileBtn
  );
});

// Add click event to Profile button
profileBtn.addEventListener("click", function () {
  toggleContent(document.getElementById("profile-content"), [
    document.getElementById("active-cases-content"),
    document.getElementById("requested-cases-content"),
    document.getElementById("completed-cases-content"),
    document.getElementById("rejected-cases-content"),
  ]);

  // Set active button style
  setActiveButton(
    profileBtn,
    caseActiveBtn,
    requestedCasesBtn,
    completedCasesBtn,
    rejectedCasesBtn
  );
});

// Set initial active button (Case Overview)
setActiveButton(
  caseActiveBtn,
  requestedCasesBtn,
  completedCasesBtn,
  rejectedCasesBtn,
  profileBtn
);

// --------------------------------------------------------------------------------------------------------------------------------------------------- appointment form step by step

// form open and close button
const addBtn = document.querySelectorAll(".add-appointment-btn");
const intakeForm = document.getElementById("intake-form");

if (addBtn && intakeForm) {
  addBtn.forEach((btn) => {
    btn.addEventListener("click", function () {
      intakeForm.classList.remove("hidden");
    });
  });
}

// close modal when clicking outside modal content
if (intakeForm) {
  intakeForm.addEventListener("click", function (e) {
    if (e.target === intakeForm) {
      intakeForm.classList.add("hidden");
    }
  });
}

const steps = document.querySelectorAll(".step-content");
const nextBtn = document.getElementById("next-btn");
const prevBtn = document.getElementById("prev-btn");
const submitBtn = document.getElementById("submit-btn");
const stepCircle = document.querySelectorAll(".step-circle");
const stepLine = document.querySelectorAll(".step-line");

let currentStep = 0;

function updateStep() {
  // Hide all steps
  steps.forEach((step, index) => {
    if (index === currentStep) {
      step.classList.remove("hidden");
    } else {
      step.classList.add("hidden");
    }
  });

    // Show "Submit" button on the last step and hide "Next"
    if (currentStep === steps.length - 1) {
      nextBtn.classList.remove("active");
      submitBtn.classList.add("active");
    } else {
      nextBtn.classList.add("active");
      submitBtn.classList.remove("active");
    }

  // Update step circle and line
  stepCircle.forEach((circle, index) => {
    const line = stepLine[index];
    if (index === currentStep) {
      circle.classList.add("active");
      line.classList.add("active");
    } else {
      circle.classList.remove("active");
      line.classList.remove("active");
    }
  });
}

function validateStep() {
  const currentStepContent = steps[currentStep];
  const requiredFields = currentStepContent.querySelectorAll("[required]");
  let isValid = true;

  // Check if all required fields are filled
  requiredFields.forEach((field) => {
    // Check for empty input
    if (!field.value.trim()) {
      field.classList.add("border-red-500");
      field.classList.remove("border-gray-300");
      isValid = false;
    } else {
      field.classList.add("border-gray-300");
      field.classList.remove("border-red-500");
    }

    // Check if it's an email field and validate its format
    if (field.type === "email" && field.value.trim()) {
      if (!field.checkValidity()) {
        field.classList.add("border-red-500");
        field.classList.remove("border-gray-300");
        isValid = false;
      } else {
        field.classList.add("border-gray-300");
        field.classList.remove("border-red-500");
      }
    }

    // Check if it's a phone number field and validate its format
    if (field.type === "tel" && field.value.trim()) {
      if (!field.checkValidity()) {
        field.classList.add("border-red-500");
        field.classList.remove("border-gray-300");
        isValid = false;
      } else {
        field.classList.add("border-gray-300");
        field.classList.remove("border-red-500");
      }
    }

    // Check if it's a date field and validate its value
    if (field.type === "date" && field.value.trim()) {
      if (!field.checkValidity()) {
        field.classList.add("border-red-500");
        field.classList.remove("border-gray-300");
        isValid = false;
      } else {
        field.classList.add("border-gray-300");
        field.classList.remove("border-red-500");
      }
    }
  });

  return isValid;
}

// Next button functionality
if (nextBtn) {
  nextBtn.addEventListener("click", function () {
    if (validateStep()) {
      if (currentStep < steps.length - 1) {
        currentStep++;
        updateStep();
      }
    }
  });
}

// Previous button functionality
if (prevBtn) {
  prevBtn.addEventListener("click", function () {
    if (currentStep > 0) {
      currentStep--;
      updateStep();
    }
  });
}

// Initialize the first step
if (steps.length > 0) {
  updateStep();
}


