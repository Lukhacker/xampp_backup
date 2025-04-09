document.addEventListener("DOMContentLoaded", function () {
    const calendar = document.getElementById("calendar");
    const eventInfo = document.getElementById("event-info");
  
    if (!calendar || !eventosData) return;
  
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
  
    for (let day = 1; day <= daysInMonth; day++) {
      const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
      const dayDiv = document.createElement("div");
      dayDiv.classList.add("calendar-day");
      dayDiv.textContent = day;
  
      if (eventosData[dateStr]) {
        dayDiv.classList.add("event");
  
        dayDiv.addEventListener("click", () => {
          const evento = eventosData[dateStr][0];
          eventInfo.innerHTML = `
            <img src="${evento.image}" alt="${evento.title}">
            <h3>${evento.title}</h3>
            <p>${evento.excerpt}</p>
            <a href="${evento.url}" class="btn-mas-info">Más info &gt;</a>
          `;
        });
      }
  
      calendar.appendChild(dayDiv);
    }
});  