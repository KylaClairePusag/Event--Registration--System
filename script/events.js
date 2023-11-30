function zoomImage(imgElement) {
  var zoomContainer = document.createElement("div");
  zoomContainer.classList.add("full-screen-zoom");

  var prevButton = document.createElement("button");
  prevButton.innerHTML = "&lt;";
  prevButton.classList.add("nav-button", "prev-button");
  prevButton.addEventListener("click", showPrevImage);
  zoomContainer.appendChild(prevButton);

  var zoomedImg = document.createElement("img");
  zoomedImg.src = imgElement.firstChild.src;
  zoomContainer.appendChild(zoomedImg);

  var nextButton = document.createElement("button");
  nextButton.innerHTML = "&gt;";
  nextButton.classList.add("nav-button", "next-button");
  nextButton.addEventListener("click", showNextImage);
  zoomContainer.appendChild(nextButton);

  document.body.appendChild(zoomContainer);

  zoomContainer.addEventListener("click", function (event) {
    if (event.target === zoomContainer) {
      document.body.removeChild(zoomContainer);
    }
  });

  var images = document.querySelectorAll(".image-item img");
  var currentIndex = Array.from(images).indexOf(imgElement.firstChild);

  function showPrevImage() {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    zoomedImg.src = images[currentIndex].src;
  }

  function showNextImage() {
    currentIndex = (currentIndex + 1) % images.length;
    zoomedImg.src = images[currentIndex].src;
  }
}
function openAttendModal(eventId) {
  document.getElementById("attendDialogEventId").value = eventId;
  document.getElementById("attendDialog").showModal();
}

function closeAttendDialog() {
  document.getElementById("attendDialog").close();
}

function openCancelModal(eventId) {
  document.getElementById("cancelDialogEventId").value = eventId;
  document.getElementById("cancelDialog").showModal();
}

function closeCancelDialog() {
  document.getElementById("cancelDialog").close();
}

document.addEventListener("click", function (event) {
  var modal = document.querySelector(".modal");
  if (event.target === modal) {
    modal.close();
  }
});
document.addEventListener("click", function (event) {
  var cancelModal = document.getElementById("cancelDialog");
  if (event.target === cancelModal) {
    cancelModal.close();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  var navLinks = document.querySelectorAll(".event-details-nav a");
  navLinks.forEach(function (link) {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      var targetId = link.getAttribute("data-target");
      showSection(targetId);
      setActiveLink(link);
    });
  });

  function showSection(sectionId) {
    var sections = document.querySelectorAll(".toggle-section");
    sections.forEach(function (section) {
      if (section.id === sectionId) {
        section.classList.add("active-section");
      } else {
        section.classList.remove("active-section");
      }
    });
  }

  function setActiveLink(clickedLink) {
    navLinks.forEach(function (link) {
      link.classList.remove("active-link");
    });
    clickedLink.classList.add("active-link");
  }
});

function openAttendModal(eventId) {
  document.getElementById("attendDialogEventId").value = eventId;
  document.getElementById("attendDialog").showModal();
}

function closeAttendDialog() {
  document.getElementById("attendDialog").close();
}

function openCancelModal(eventId) {
  document.getElementById("cancelDialogEventId").value = eventId;
  document.getElementById("cancelDialog").showModal();
}

function closeCancelDialog() {
  document.getElementById("cancelDialog").close();
}

document.addEventListener("click", function (event) {
  var modal = document.querySelector(".modal");
  if (event.target === modal) {
    modal.close();
  }
});
document.addEventListener("click", function (event) {
  var cancelModal = document.getElementById("cancelDialog");
  if (event.target === cancelModal) {
    cancelModal.close();
  }
});
