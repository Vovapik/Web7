// app.js

const playButton = document.getElementById('play');
const workArea = document.getElementById('work');
const closeButton = document.getElementById('close');
const animArea = document.getElementById('anim');
const startButton = document.getElementById('start');
const stopButton = document.getElementById('stop');
const reloadButton = document.getElementById('reload');
const logTable = document.getElementById('logTable');
const messagesDiv = document.getElementById('messages');

let redCircle, greenCircle;
let animationId;
let redDirX = 1, redDirY = 1;
let greenDirX = 1, greenDirY = 1;
let eventCounter = 0;

logTable.style.display = 'none';

const createCircle = (color, left, top) => {
  const circle = document.createElement('div');
  circle.classList.add('circle', color);
  circle.style.left = left + 'px';
  circle.style.top = top + 'px';
  animArea.appendChild(circle);
  return circle;
};

const showMessage = (message) => {
  messagesDiv.textContent = message;
};

const logEvent = async (message, method = 'immediate') => {
  const timestamp = new Date().toISOString();
  const localTime = new Date().toLocaleString();
  const event = { id: ++eventCounter, timestamp, localTime, message, method };

  showMessage(message);

  if (method === 'immediate') {
    await sendEventToServer(event);
  } else {
    accumulateEventLocally(event);
  }

  updateLogTable(event);
};

const sendEventToServer = async (event) => {
  try {
    await fetch('save_event.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(event),
    });
  } catch (error) {
    console.error('Error sending event to server:', error);
  }
};

const accumulateEventLocally = (event) => {
  const events = JSON.parse(localStorage.getItem('accumulatedEvents')) || [];
  events.push(event);
  localStorage.setItem('accumulatedEvents', JSON.stringify(events));
};

const sendAccumulatedEvents = async () => {
  const events = JSON.parse(localStorage.getItem('accumulatedEvents')) || [];
  if (events.length > 0) {
    try {
      const response = await fetch('save_accumulated.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(events),
      });
      const result = await response.json();
      if (result.success) {
        localStorage.removeItem('accumulatedEvents');
      } else {
        console.error('Error saving accumulated events:', result.error);
      }
    } catch (error) {
      console.error('Error sending accumulated events:', error);
    }
  }
};


const updateLogTable = (event) => {
  const logRow = `<tr><td>${event.id}</td><td>${event.localTime}</td><td>${event.message}</td><td>${event.method}</td></tr>`;
  if (!logTable.querySelector('table')) {
    logTable.innerHTML = `
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Time</th>
            <th>Event</th>
            <th>Method</th>
          </tr>
        </thead>
        <tbody>
          ${logRow}
        </tbody>
      </table>
    `;
  } else {
    logTable.querySelector('tbody').innerHTML += logRow;
  }
};

const startAnimation = () => {
  const animRect = animArea.getBoundingClientRect();

  const moveCircle = (circle, dirX, dirY) => {
    const circleRect = circle.getBoundingClientRect();

    if (circleRect.right >= animRect.right || circleRect.left <= animRect.left) dirX *= -1;
    if (circleRect.bottom >= animRect.bottom || circleRect.top <= animRect.top) dirY *= -1;

    circle.style.left = circle.offsetLeft + dirX + 'px';
    circle.style.top = circle.offsetTop + dirY + 'px';

    return [dirX, dirY];
  };

  animationId = setInterval(() => {
    [redDirX, redDirY] = moveCircle(redCircle, redDirX, redDirY);
    [greenDirX, greenDirY] = moveCircle(greenCircle, greenDirX, greenDirY);

    logEvent('Circles moved', 'immediate');

    const redRect = redCircle.getBoundingClientRect();
    const greenRect = greenCircle.getBoundingClientRect();

    if (
      redRect.left < greenRect.right &&
      redRect.right > greenRect.left &&
      redRect.top < greenRect.bottom &&
      redRect.bottom > greenRect.top
    ) {
      clearInterval(animationId);
      logEvent('Circles collided!', 'immediate');
      stopButton.style.display = 'none';
      reloadButton.style.display = 'inline';
    }
  }, 10);
};

playButton.addEventListener('click', () => {
  workArea.style.display = 'flex';
  logTable.style.display = 'none';
  const animWidth = animArea.clientWidth;
  const animHeight = animArea.clientHeight;

  redCircle = createCircle('red', 0, Math.random() * (animHeight - 20));
  greenCircle = createCircle('green', Math.random() * (animWidth - 20), 0);

  logEvent('Play button clicked', 'immediate');

  startButton.style.display = 'inline';
  stopButton.style.display = 'none';
  reloadButton.style.display = 'none';
});

closeButton.addEventListener('click', () => {
  workArea.style.display = 'none';
  animArea.innerHTML = '';
  clearInterval(animationId);
  logTable.style.display = 'block';
  logEvent('Close button clicked', 'immediate');

  sendAccumulatedEvents();
});

startButton.addEventListener('click', () => {
  startAnimation();
  startButton.style.display = 'none';
  stopButton.style.display = 'inline';
  logEvent('Start button clicked', 'immediate');
});

stopButton.addEventListener('click', () => {
  clearInterval(animationId);
  stopButton.style.display = 'none';
  startButton.style.display = 'inline';
  logEvent('Stop button clicked', 'immediate');
});

reloadButton.addEventListener('click', () => {
  animArea.innerHTML = '';
  const animWidth = animArea.clientWidth;
  const animHeight = animArea.clientHeight;

  redCircle = createCircle('red', 0, Math.random() * (animHeight - 20));
  greenCircle = createCircle('green', Math.random() * (animWidth - 20), 0);

  reloadButton.style.display = 'none';
  startButton.style.display = 'inline';
  redDirX = 1; redDirY = 1;
  greenDirX = 1; greenDirY = 1;
  logEvent('Reload button clicked', 'immediate');
});
