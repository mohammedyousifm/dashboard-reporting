<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\WorkLog;
use App\Models\Achievement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create(['name' => 'Admin', 'email' => 'admin@example.com']);

        // Categories
        $categories = [
            ['name' => 'Development',    'color' => '#4f46e5'],
            ['name' => 'Microsoft 365',  'color' => '#0ea5e9'],
            ['name' => 'Intune',         'color' => '#7c3aed'],
            ['name' => 'Research',       'color' => '#f59e0b'],
            ['name' => 'Documentation',  'color' => '#10b981'],
            ['name' => 'Meetings',       'color' => '#ef4444'],
            ['name' => 'Support',        'color' => '#f97316'],
            ['name' => 'Company Registration', 'color' => '#06b6d4'],
        ];
        foreach ($categories as $cat) Category::create($cat);

        // Companies
        $companies = [
            Company::create(['name' => 'Acme Corp',       'industry' => 'Technology',  'contact_email' => 'contact@acme.com',       'status' => 'active']),
            Company::create(['name' => 'GlobalTech Ltd',  'industry' => 'IT Services', 'contact_email' => 'info@globaltech.com',     'status' => 'active']),
            Company::create(['name' => 'NovaSystems',     'industry' => 'Software',    'contact_email' => 'hello@novasystems.com',   'status' => 'active']),
            Company::create(['name' => 'Brightway Inc',   'industry' => 'Finance',     'contact_email' => 'hr@brightway.com',        'status' => 'active']),
            Company::create(['name' => 'TechEdge Group',  'industry' => 'Consulting',  'contact_email' => 'ops@techedge.com',        'status' => 'active']),
            Company::create(['name' => 'CloudPoint',      'industry' => 'Cloud',       'contact_email' => 'support@cloudpoint.com',  'status' => 'active']),
        ];

        // Employees
        $employees = [
            Employee::create(['name' => 'John Smith',   'email' => 'john@example.com',   'position' => 'Senior Developer',   'department' => 'Engineering', 'hire_date' => '2023-01-15', 'status' => 'active']),
            Employee::create(['name' => 'Sarah Jones',  'email' => 'sarah@example.com',  'position' => 'Project Manager',    'department' => 'Management',  'hire_date' => '2022-06-01', 'status' => 'active']),
            Employee::create(['name' => 'Mike Brown',   'email' => 'mike@example.com',   'position' => 'IT Consultant',      'department' => 'IT',          'hire_date' => '2024-03-10', 'status' => 'active']),
        ];

        // Projects
        $catIds = Category::pluck('id', 'name');
        $projects = [];
        $projectData = [
            ['name' => 'DUNS Registration',      'company' => 0, 'cat' => 'Company Registration', 'status' => 'completed'],
            ['name' => 'M365 Deployment',        'company' => 1, 'cat' => 'Microsoft 365',        'status' => 'active'],
            ['name' => 'Intune MDM Setup',       'company' => 2, 'cat' => 'Intune',               'status' => 'active'],
            ['name' => 'Cloud Infrastructure',   'company' => 3, 'cat' => 'Development',          'status' => 'active'],
            ['name' => 'Internal Portal',        'company' => 4, 'cat' => 'Development',          'status' => 'active'],
            ['name' => 'Security Audit',         'company' => 5, 'cat' => 'Support',              'status' => 'on_hold'],
            ['name' => 'Data Migration',         'company' => 0, 'cat' => 'Development',          'status' => 'active'],
            ['name' => 'Support Helpdesk',       'company' => 1, 'cat' => 'Support',              'status' => 'active'],
        ];
        foreach ($projectData as $pd) {
            $projects[] = Project::create([
                'name'        => $pd['name'],
                'company_id'  => $companies[$pd['company']]->id,
                'category_id' => $catIds[$pd['cat']],
                'status'      => $pd['status'],
                'start_date'  => Carbon::now()->subMonths(rand(2,8))->startOfMonth(),
            ]);
        }

        // Tasks
        $statuses = ['pending','in_progress','completed'];
        $priorities = ['low','medium','high'];
        foreach ($projects as $project) {
            for ($i = 0; $i < rand(3, 8); $i++) {
                $status = $statuses[array_rand($statuses)];
                Task::create([
                    'title'          => "Task $i for {$project->name}",
                    'project_id'     => $project->id,
                    'employee_id'    => $employees[array_rand($employees)]->id,
                    'category_id'    => $project->category_id,
                    'status'         => $status,
                    'priority'       => $priorities[array_rand($priorities)],
                    'due_date'       => Carbon::now()->addDays(rand(-10, 30)),
                    'completed_date' => $status === 'completed' ? Carbon::now()->subDays(rand(1,15)) : null,
                ]);
            }
        }

        // Work logs — last 6 months
        $allCatIds = Category::pluck('id')->toArray();
        for ($day = 180; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            if ($date->isWeekend()) continue;
            foreach ($employees as $emp) {
                if (rand(0,10) < 2) continue;
                $project = $projects[array_rand($projects)];
                WorkLog::create([
                    'employee_id' => $emp->id,
                    'project_id'  => $project->id,
                    'category_id' => $project->category_id ?? $allCatIds[array_rand($allCatIds)],
                    'log_date'    => $date->toDateString(),
                    'hours'       => round(rand(2, 8) + (rand(0,3) * 0.25), 2),
                    'description' => "Worked on {$project->name}",
                ]);
            }
        }

        // Achievements
        $achievementData = [
            ['title' => 'DUNS Registration Completed',         'type' => 'milestone'],
            ['title' => 'M365 Go-Live Successful',             'type' => 'milestone'],
            ['title' => 'Intune MDM Fully Deployed',           'type' => 'milestone'],
            ['title' => 'Cloud Infrastructure Launched',       'type' => 'milestone'],
            ['title' => 'Azure Certification',                 'type' => 'certification'],
            ['title' => 'Employee of the Month',               'type' => 'award'],
            ['title' => 'Zero Support Tickets Week',           'type' => 'recognition'],
            ['title' => 'Security Audit Passed',               'type' => 'milestone'],
        ];
        foreach ($achievementData as $i => $ad) {
            Achievement::create([
                'title'         => $ad['title'],
                'employee_id'   => $employees[$i % count($employees)]->id,
                'project_id'    => $projects[$i % count($projects)]->id,
                'achieved_date' => Carbon::now()->subDays(rand(5, 120))->toDateString(),
                'type'          => $ad['type'],
            ]);
        }
    }
}
