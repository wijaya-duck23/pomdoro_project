<?php
require_once __DIR__ . '/../../config/database.php';

class SessionModel {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    public function saveSession($type, $duration, $startTime, $endTime, $userId = null) {
        $sql = "INSERT INTO sessions (user_id, type, duration, start_time, end_time) 
                VALUES (:user_id, :type, :duration, :start_time, :end_time)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
            $stmt->bindParam(':start_time', $startTime);
            $stmt->bindParam(':end_time', $endTime);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllSessions($userId = null) {
        try {
            if ($userId) {
                $sql = "SELECT * FROM sessions WHERE user_id = :user_id ORDER BY start_time DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();
            } else {
                $sql = "SELECT * FROM sessions ORDER BY start_time DESC";
                $stmt = $this->db->query($sql);
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getSessionStats($userId = null) {
        try {
            $conditions = $userId ? "WHERE user_id = :user_id" : "";
            
            $sql = "SELECT 
                    COUNT(CASE WHEN type = 'pomodoro' THEN 1 END) as total_pomodoros,
                    SUM(CASE WHEN type = 'pomodoro' THEN duration ELSE 0 END) as total_work_time,
                    SUM(CASE WHEN type IN ('short_break', 'long_break') THEN duration ELSE 0 END) as total_break_time
                    FROM sessions $conditions";
            
            $stmt = $this->db->prepare($sql);
            
            if ($userId) {
                $stmt->bindParam(':user_id', $userId);
            }
            
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [
                'total_pomodoros' => 0,
                'total_work_time' => 0,
                'total_break_time' => 0
            ];
        }
    }
} 