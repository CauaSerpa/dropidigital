// document.addEventListener('DOMContentLoaded', function () {
// var steps = document.querySelectorAll('.step');
// var stepForms = document.querySelectorAll('.step-form');
// var currentStep = 0;

// updateStep();

// function updateStep() {
//     steps.forEach(function (step, index) {
//         if (index <= currentStep) {
//             step.classList.add('active');
//         } else {
//             step.classList.remove('active');
//         }
//     });

//     stepForms.forEach(function (stepForm, index) {
//         if (index === currentStep) {
//             stepForm.classList.add('active');
//         } else {
//             stepForm.classList.remove('active');
//         }
//     });
// }

// document.querySelectorAll('.next').forEach(function (nextBtn) {
//         nextBtn.addEventListener('click', function (e) {
//         currentStep++;
//         updateStep();
//     });
// });

// document.querySelectorAll('.prev').forEach(function (prevBtn) {
//     prevBtn.addEventListener('click', function (e) {
//         e.preventDefault();
//         currentStep--;
//         updateStep();
//     });
// });

// });