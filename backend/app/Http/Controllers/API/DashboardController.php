<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics with dummy random data
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            // Generate random dummy data for dashboard stats
            $stats = [
                'totalUsers' => rand(100, 500),
                'totalRoles' => rand(5, 15),
                'activeUsers' => rand(20, 100),
                'permissions' => rand(15, 50),
                'newUsersToday' => rand(0, 20),
                'newUsersThisWeek' => rand(5, 50),
                'newUsersThisMonth' => rand(20, 150),
                'topRoles' => [
                    [
                        'name' => 'Admin',
                        'count' => rand(1, 5),
                        'percentage' => rand(5, 15)
                    ],
                    [
                        'name' => 'Manager',
                        'count' => rand(5, 20),
                        'percentage' => rand(15, 35)
                    ],
                    [
                        'name' => 'Employee',
                        'count' => rand(20, 100),
                        'percentage' => rand(40, 70)
                    ],
                    [
                        'name' => 'Guest',
                        'count' => rand(10, 50),
                        'percentage' => rand(10, 25)
                    ]
                ],
                'systemStatus' => [
                    'database' => ['status' => 'online', 'response_time' => rand(5, 25) . 'ms'],
                    'api_server' => ['status' => 'online', 'response_time' => rand(10, 50) . 'ms'],
                    'cache' => ['status' => rand(0, 10) > 7 ? 'warning' : 'online', 'response_time' => rand(1, 10) . 'ms'],
                    'storage' => ['status' => 'online', 'usage' => rand(30, 85) . '%']
                ],
                'recentActivity' => [
                    [
                        'id' => 1,
                        'icon' => 'person_add',
                        'color' => 'primary',
                        'text' => 'New user "' . $this->generateRandomName() . '" was created',
                        'time' => rand(1, 5) . ' minutes ago'
                    ],
                    [
                        'id' => 2,
                        'icon' => 'edit',
                        'color' => 'secondary',
                        'text' => 'User "' . $this->generateRandomName() . '" updated their profile',
                        'time' => rand(1, 3) . ' hours ago'
                    ],
                    [
                        'id' => 3,
                        'icon' => 'admin_panel_settings',
                        'color' => 'positive',
                        'text' => 'Role "' . $this->generateRandomRole() . '" permissions updated',
                        'time' => rand(2, 8) . ' hours ago'
                    ],
                    [
                        'id' => 4,
                        'icon' => 'login',
                        'color' => 'info',
                        'text' => 'User "' . $this->generateRandomName() . '" logged in',
                        'time' => rand(3, 12) . ' hours ago'
                    ],
                    [
                        'id' => 5,
                        'icon' => 'security',
                        'color' => 'warning',
                        'text' => 'Security settings updated',
                        'time' => rand(1, 3) . ' days ago'
                    ]
                ],
                'growthMetrics' => [
                    'userGrowth' => [
                        'current' => rand(100, 500),
                        'previous' => rand(80, 450),
                        'percentage' => rand(5, 25)
                    ],
                    'roleUsage' => [
                        'current' => rand(5, 15),
                        'previous' => rand(4, 12),
                        'percentage' => rand(0, 15)
                    ],
                    'activeUsers' => [
                        'current' => rand(20, 100),
                        'previous' => rand(15, 85),
                        'percentage' => rand(3, 20)
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard statistics retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate random user names for dummy data
     *
     * @return string
     */
    private function generateRandomName(): string
    {
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Chris', 'Jessica', 'Ryan', 'Amanda', 'Kevin', 'Lisa', 'Daniel', 'Maria', 'James'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * Generate random role names for dummy data
     *
     * @return string
     */
    private function generateRandomRole(): string
    {
        $roles = ['Admin', 'Manager', 'Supervisor', 'Employee', 'Editor', 'Viewer', 'Moderator', 'Analyst', 'Coordinator', 'Specialist'];

        return $roles[array_rand($roles)];
    }
}
