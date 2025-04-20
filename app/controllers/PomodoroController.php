<?php
require_once __DIR__ . '/../models/SessionModel.php';

class PomodoroController {
    private $sessionModel;
    
    public function __construct() {
        $this->sessionModel = new SessionModel();
    }
    
    public function showDashboard() {
        // Get session statistics
        $stats = $this->sessionModel->getSessionStats();
        $sessions = $this->sessionModel->getAllSessions();
        
        // Include the dashboard view
        include __DIR__ . '/../views/dashboard.php';
    }
    
    public function saveSession() {
        // Validate and sanitize input
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);
        $startTime = filter_input(INPUT_POST, 'startTime', FILTER_SANITIZE_STRING);
        $endTime = filter_input(INPUT_POST, 'endTime', FILTER_SANITIZE_STRING);
        
        // Validate required fields
        if (!$type || !$duration || !$startTime || !$endTime) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }
        
        // Save session to database
        $result = $this->sessionModel->saveSession($type, $duration, $startTime, $endTime);
        
        // Return result as JSON
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save session']);
        }
    }
    
    public function getSessions() {
        // Get all sessions from the database
        $sessions = $this->sessionModel->getAllSessions();
        $stats = $this->sessionModel->getSessionStats();
        
        // Return sessions as JSON
        header('Content-Type: application/json');
        echo json_encode([
            'sessions' => $sessions,
            'stats' => $stats
        ]);
    }
} 