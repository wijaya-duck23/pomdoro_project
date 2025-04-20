<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pomodoro Timer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <header class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800">Pomodoro Timer</h1>
            <p class="text-gray-600">Stay focused and productive</p>
        </header>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Timer Section -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">Timer</h2>
                    <div class="flex space-x-2">
                        <button id="pomodoro-btn" class="px-3 py-1 bg-red-500 text-white rounded-md active">Pomodoro</button>
                        <button id="short-break-btn" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md">Short Break</button>
                        <button id="long-break-btn" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md">Long Break</button>
                    </div>
                </div>
                
                <div class="text-center py-10">
                    <div id="timer" class="text-7xl font-bold text-gray-800 mb-8">25:00</div>
                    <div class="flex justify-center space-x-4">
                        <button id="start-btn" class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                            <i class="fas fa-play mr-2"></i>Start
                        </button>
                        <button id="pause-btn" class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition hidden">
                            <i class="fas fa-pause mr-2"></i>Pause
                        </button>
                        <button id="resume-btn" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition hidden">
                            <i class="fas fa-play mr-2"></i>Resume
                        </button>
                        <button id="reset-btn" class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                            <i class="fas fa-redo-alt mr-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Statistics</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                        <span class="text-gray-600">Pomodoros Completed</span>
                        <span id="total-pomodoros" class="font-semibold text-gray-800"><?= $stats['total_pomodoros'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                        <span class="text-gray-600">Total Focus Time</span>
                        <span id="total-focus-time" class="font-semibold text-gray-800"><?= formatTime($stats['total_work_time'] ?? 0) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Break Time</span>
                        <span id="total-break-time" class="font-semibold text-gray-800"><?= formatTime($stats['total_break_time'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- History Section -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Session History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="py-2 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                            <th class="py-2 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="py-2 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody">
                        <?php foreach ($sessions as $session): ?>
                        <tr class="border-b border-gray-200">
                            <td class="py-2 px-4">
                                <?php 
                                    $typeClass = '';
                                    switch ($session['type']) {
                                        case 'pomodoro':
                                            $typeClass = 'bg-red-100 text-red-800';
                                            break;
                                        case 'short_break':
                                            $typeClass = 'bg-green-100 text-green-800';
                                            break;
                                        case 'long_break':
                                            $typeClass = 'bg-blue-100 text-blue-800';
                                            break;
                                    }
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $typeClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $session['type'])) ?>
                                </span>
                            </td>
                            <td class="py-2 px-4"><?= formatTime($session['duration']) ?></td>
                            <td class="py-2 px-4"><?= date('Y-m-d', strtotime($session['start_time'])) ?></td>
                            <td class="py-2 px-4"><?= date('H:i:s', strtotime($session['start_time'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="/public/js/timer.js"></script>
</body>
</html>

<?php
// Helper function to format seconds into HH:MM:SS
function formatTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds / 60) % 60);
    $seconds = $seconds % 60;
    
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
?> 