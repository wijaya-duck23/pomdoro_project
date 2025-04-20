// Pomodoro Timer JavaScript

// DOM Elements
const timerDisplay = document.getElementById('timer');
const startBtn = document.getElementById('start-btn');
const pauseBtn = document.getElementById('pause-btn');
const resumeBtn = document.getElementById('resume-btn');
const resetBtn = document.getElementById('reset-btn');
const pomodoroBtn = document.getElementById('pomodoro-btn');
const shortBreakBtn = document.getElementById('short-break-btn');
const longBreakBtn = document.getElementById('long-break-btn');

// Timer settings (in seconds)
const TIMER_SETTINGS = {
    pomodoro: 25 * 60,
    short_break: 5 * 60,
    long_break: 15 * 60
};

// Timer state
let timerType = 'pomodoro';
let timeLeft = TIMER_SETTINGS[timerType];
let timerId = null;
let isRunning = false;
let isPaused = false;
let startTime = null;

// Initialize timer display
updateTimerDisplay();

// Event Listeners
startBtn.addEventListener('click', startTimer);
pauseBtn.addEventListener('click', pauseTimer);
resumeBtn.addEventListener('click', resumeTimer);
resetBtn.addEventListener('click', resetTimer);

pomodoroBtn.addEventListener('click', () => changeTimerType('pomodoro'));
shortBreakBtn.addEventListener('click', () => changeTimerType('short_break'));
longBreakBtn.addEventListener('click', () => changeTimerType('long_break'));

// Functions
function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    document.title = `${timerDisplay.textContent} - Pomodoro Timer`;
}

function startTimer() {
    if (isRunning) return;
    
    isRunning = true;
    isPaused = false;
    startTime = new Date().toISOString();
    
    // Update button states
    startBtn.classList.add('hidden');
    pauseBtn.classList.remove('hidden');
    resumeBtn.classList.add('hidden');
    
    // Disable timer type buttons during active session
    toggleTimerTypeButtons(false);
    
    // Start countdown
    timerId = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            completeTimer();
        }
    }, 1000);
}

function pauseTimer() {
    if (!isRunning || isPaused) return;
    
    clearInterval(timerId);
    isPaused = true;
    
    // Update button states
    pauseBtn.classList.add('hidden');
    resumeBtn.classList.remove('hidden');
}

function resumeTimer() {
    if (!isPaused) return;
    
    isPaused = false;
    
    // Update button states
    resumeBtn.classList.add('hidden');
    pauseBtn.classList.remove('hidden');
    
    // Continue countdown
    timerId = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            completeTimer();
        }
    }, 1000);
}

function resetTimer() {
    clearInterval(timerId);
    isRunning = false;
    isPaused = false;
    
    // Reset timer to current type's duration
    timeLeft = TIMER_SETTINGS[timerType];
    updateTimerDisplay();
    
    // Update button states
    startBtn.classList.remove('hidden');
    pauseBtn.classList.add('hidden');
    resumeBtn.classList.add('hidden');
    
    // Re-enable timer type buttons
    toggleTimerTypeButtons(true);
}

function completeTimer() {
    clearInterval(timerId);
    
    // Play notification sound
    playNotificationSound();
    
    // Save session to database
    saveSession(timerType, TIMER_SETTINGS[timerType], startTime, new Date().toISOString());
    
    // Auto transition to next timer type
    if (timerType === 'pomodoro') {
        changeTimerType('short_break');
    } else {
        changeTimerType('pomodoro');
    }
    
    // Reset timer state
    isRunning = false;
    isPaused = false;
    
    // Update button states
    startBtn.classList.remove('hidden');
    pauseBtn.classList.add('hidden');
    resumeBtn.classList.add('hidden');
    
    // Re-enable timer type buttons
    toggleTimerTypeButtons(true);
    
    // Update stats
    updateStats();
    
    // Browser notification
    showNotification();
}

function changeTimerType(type) {
    if (isRunning && !isPaused) return;
    
    // Update active button styling
    pomodoroBtn.classList.toggle('bg-red-500', type === 'pomodoro');
    pomodoroBtn.classList.toggle('text-white', type === 'pomodoro');
    pomodoroBtn.classList.toggle('bg-gray-300', type !== 'pomodoro');
    pomodoroBtn.classList.toggle('text-gray-700', type !== 'pomodoro');
    
    shortBreakBtn.classList.toggle('bg-green-500', type === 'short_break');
    shortBreakBtn.classList.toggle('text-white', type === 'short_break');
    shortBreakBtn.classList.toggle('bg-gray-300', type !== 'short_break');
    shortBreakBtn.classList.toggle('text-gray-700', type !== 'short_break');
    
    longBreakBtn.classList.toggle('bg-blue-500', type === 'long_break');
    longBreakBtn.classList.toggle('text-white', type === 'long_break');
    longBreakBtn.classList.toggle('bg-gray-300', type !== 'long_break');
    longBreakBtn.classList.toggle('text-gray-700', type !== 'long_break');
    
    // Update timer type and reset timer
    timerType = type;
    
    // If timer was in progress, save partial session
    if (isRunning && startTime) {
        const duration = TIMER_SETTINGS[timerType] - timeLeft;
        if (duration > 0) {
            saveSession(timerType, duration, startTime, new Date().toISOString());
        }
    }
    
    resetTimer();
}

function toggleTimerTypeButtons(enabled) {
    const buttons = [pomodoroBtn, shortBreakBtn, longBreakBtn];
    buttons.forEach(btn => {
        btn.disabled = !enabled;
        btn.classList.toggle('opacity-50', !enabled);
        btn.classList.toggle('cursor-not-allowed', !enabled);
    });
}

function saveSession(type, duration, startTime, endTime) {
    const formData = new FormData();
    formData.append('type', type);
    formData.append('duration', duration);
    formData.append('startTime', startTime);
    formData.append('endTime', endTime);
    
    fetch('/api/saveSession', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStats();
        }
    })
    .catch(error => console.error('Error saving session:', error));
}

function updateStats() {
    fetch('/api/sessions')
    .then(response => response.json())
    .then(data => {
        // Update statistics
        document.getElementById('total-pomodoros').textContent = data.stats.total_pomodoros || 0;
        
        // Format time from seconds to HH:MM:SS
        const formatTime = (seconds) => {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds / 60) % 60);
            const secs = seconds % 60;
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        };
        
        document.getElementById('total-focus-time').textContent = formatTime(data.stats.total_work_time || 0);
        document.getElementById('total-break-time').textContent = formatTime(data.stats.total_break_time || 0);
        
        // Update session history
        const historyTbody = document.getElementById('history-tbody');
        historyTbody.innerHTML = '';
        
        data.sessions.forEach(session => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200';
            
            // Type column with colored badge
            let typeClass = '';
            switch (session.type) {
                case 'pomodoro':
                    typeClass = 'bg-red-100 text-red-800';
                    break;
                case 'short_break':
                    typeClass = 'bg-green-100 text-green-800';
                    break;
                case 'long_break':
                    typeClass = 'bg-blue-100 text-blue-800';
                    break;
            }
            
            const formattedType = session.type.replace('_', ' ');
            const date = new Date(session.start_time);
            
            row.innerHTML = `
                <td class="py-2 px-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${typeClass}">
                        ${formattedType.charAt(0).toUpperCase() + formattedType.slice(1)}
                    </span>
                </td>
                <td class="py-2 px-4">${formatTime(session.duration)}</td>
                <td class="py-2 px-4">${date.toLocaleDateString()}</td>
                <td class="py-2 px-4">${date.toLocaleTimeString()}</td>
            `;
            
            historyTbody.appendChild(row);
        });
    })
    .catch(error => console.error('Error updating stats:', error));
}

function playNotificationSound() {
    const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-achievement-bell-600.mp3');
    audio.play().catch(error => console.warn('Could not play notification sound:', error));
}

function showNotification() {
    if ('Notification' in window && Notification.permission === 'granted') {
        const title = timerType === 'pomodoro' ? 'Break Time!' : 'Time to Focus!';
        const message = timerType === 'pomodoro' ? 'Great job! Take a break.' : 'Break is over. Time to focus!';
        
        new Notification(title, {
            body: message,
            icon: '/favicon.ico'
        });
    } else if ('Notification' in window && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }
}

// Request notification permission when page loads
if ('Notification' in window) {
    Notification.requestPermission();
} 