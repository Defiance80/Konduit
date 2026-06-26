<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Deliverable;
use App\Models\Project;
use App\Models\Retainer;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = ['super_admin', 'agency_admin', 'agency_member', 'client_contact'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Tenant (Agency)
        $tenant = Tenant::create([
            'id'     => 'blue-wolf-agency',
            'name'   => 'Blue Wolf Agency',
            'slug'   => 'blue-wolf-agency',
            'email'  => 'hello@bluewolf.agency',
            'phone'  => '+1 555-0100',
            'website' => 'https://bluewolf.agency',
            'timezone' => 'America/New_York',
            'plan'   => 'professional',
            'status' => 'active',
        ]);

        // Super admin (no tenant)
        $superAdmin = User::create([
            'name'      => 'Super Admin',
            'email'     => 'superadmin@konduit.app',
            'password'  => Hash::make('password'),
            'user_type' => 'super_admin',
            'job_title' => 'Platform Administrator',
        ]);
        $superAdmin->assignRole('super_admin');

        // Agency admin
        $agencyAdmin = User::create([
            'name'      => 'Alex Morgan',
            'email'     => 'alex@bluewolf.agency',
            'password'  => Hash::make('password'),
            'user_type' => 'agency_user',
            'tenant_id' => $tenant->id,
            'job_title' => 'Agency Director',
            'phone'     => '+1 555-0101',
        ]);
        $agencyAdmin->assignRole('agency_admin');

        // Agency member
        $agencyMember = User::create([
            'name'      => 'Jordan Lee',
            'email'     => 'jordan@bluewolf.agency',
            'password'  => Hash::make('password'),
            'user_type' => 'agency_user',
            'tenant_id' => $tenant->id,
            'job_title' => 'Project Manager',
            'phone'     => '+1 555-0102',
        ]);
        $agencyMember->assignRole('agency_member');

        // Clients
        $clients = [
            [
                'name'       => 'Apex Commerce Solutions',
                'slug'       => 'apex-commerce-solutions',
                'email'      => 'contact@apexcommerce.com',
                'phone'      => '+1 555-0200',
                'website'    => 'https://apexcommerce.com',
                'industry'   => 'E-commerce',
                'status'     => 'active',
                'health_score' => 87,
                'notes'      => 'Long-term retainer client. Shopify + SEO focus.',
            ],
            [
                'name'       => 'Meridian Health Group',
                'slug'       => 'meridian-health-group',
                'email'      => 'marketing@meridianhealth.com',
                'phone'      => '+1 555-0300',
                'website'    => 'https://meridianhealth.com',
                'industry'   => 'Healthcare',
                'status'     => 'active',
                'health_score' => 72,
                'notes'      => 'Monthly content + PPC management.',
            ],
            [
                'name'       => 'Solis Real Estate Partners',
                'slug'       => 'solis-real-estate',
                'email'      => 'hello@solispartners.com',
                'phone'      => '+1 555-0400',
                'website'    => 'https://solispartners.com',
                'industry'   => 'Real Estate',
                'status'     => 'prospect',
                'health_score' => 55,
                'notes'      => 'New prospect — proposal sent.',
            ],
        ];

        $createdClients = [];
        foreach ($clients as $clientData) {
            $createdClients[] = Client::create(array_merge($clientData, ['tenant_id' => $tenant->id]));
        }

        [$apex, $meridian, $solis] = $createdClients;

        // Client contact users
        $apexContact = User::create([
            'name'      => 'Casey Williams',
            'email'     => 'casey@apexcommerce.com',
            'password'  => Hash::make('password'),
            'user_type' => 'client_contact',
            'tenant_id' => $tenant->id,
            'client_id' => $apex->id,
            'job_title' => 'Marketing Manager',
        ]);
        $apexContact->assignRole('client_contact');

        // Retainers
        $apexRetainer = Retainer::create([
            'tenant_id'      => $tenant->id,
            'client_id'      => $apex->id,
            'name'           => 'Apex Full-Service Retainer',
            'description'    => 'Monthly SEO, PPC, and content production for Apex Commerce.',
            'monthly_value'  => 4500,
            'hours_included' => 40,
            'start_date'     => '2025-01-01',
            'status'         => 'active',
            'billing_cycle'  => 'monthly',
            'services'       => ['SEO', 'PPC', 'Content Marketing'],
        ]);

        $meridianRetainer = Retainer::create([
            'tenant_id'      => $tenant->id,
            'client_id'      => $meridian->id,
            'name'           => 'Meridian Content & PPC Package',
            'description'    => 'Content strategy plus Google Ads management.',
            'monthly_value'  => 2800,
            'hours_included' => 25,
            'start_date'     => '2025-03-01',
            'status'         => 'active',
            'billing_cycle'  => 'monthly',
            'services'       => ['Content Marketing', 'PPC'],
        ]);

        // Projects
        $project1 = Project::create([
            'tenant_id'   => $tenant->id,
            'client_id'   => $apex->id,
            'retainer_id' => $apexRetainer->id,
            'owner_id'    => $agencyMember->id,
            'name'        => 'Apex Q3 SEO Campaign',
            'slug'        => 'apex-q3-seo-campaign',
            'description' => 'Comprehensive Q3 SEO overhaul targeting top 20 commercial keywords.',
            'status'      => 'active',
            'priority'    => 'high',
            'budget'      => 8000,
            'budget_spent' => 3200,
            'progress'    => 42,
            'start_date'  => '2026-06-01',
            'due_date'    => '2026-09-30',
        ]);

        $project2 = Project::create([
            'tenant_id'   => $tenant->id,
            'client_id'   => $apex->id,
            'retainer_id' => $apexRetainer->id,
            'owner_id'    => $agencyMember->id,
            'name'        => 'Apex Website Redesign',
            'slug'        => 'apex-website-redesign',
            'description' => 'Full Shopify store redesign with improved conversion funnel.',
            'status'      => 'on_hold',
            'priority'    => 'medium',
            'budget'      => 12000,
            'budget_spent' => 1500,
            'progress'    => 15,
            'start_date'  => '2026-07-01',
            'due_date'    => '2026-10-31',
        ]);

        $project3 = Project::create([
            'tenant_id'   => $tenant->id,
            'client_id'   => $meridian->id,
            'retainer_id' => $meridianRetainer->id,
            'owner_id'    => $agencyAdmin->id,
            'name'        => 'Meridian Blog Content Series',
            'slug'        => 'meridian-blog-content-series',
            'description' => '12-part blog series on preventive care and wellness.',
            'status'      => 'active',
            'priority'    => 'medium',
            'budget'      => 3600,
            'budget_spent' => 900,
            'progress'    => 25,
            'start_date'  => '2026-05-01',
            'due_date'    => '2026-08-31',
        ]);

        // Tickets
        $ticket1 = Ticket::create([
            'tenant_id'     => $tenant->id,
            'client_id'     => $apex->id,
            'project_id'    => $project1->id,
            'assignee_id'   => $agencyMember->id,
            'submitted_by'  => $apexContact->id,
            'subject'       => 'Homepage not ranking for primary keyword',
            'description'   => 'Our homepage dropped from position 4 to 18 for "buy running shoes online" over the past week. Need urgent investigation.',
            'type'          => 'bug',
            'status'        => 'open',
            'priority'      => 'urgent',
        ]);

        TicketComment::create([
            'ticket_id'   => $ticket1->id,
            'user_id'     => $agencyMember->id,
            'body'        => 'Running initial diagnosis — checking Google Search Console for crawl errors and manual actions.',
            'is_internal' => true,
        ]);

        TicketComment::create([
            'ticket_id'   => $ticket1->id,
            'user_id'     => $agencyMember->id,
            'body'        => 'Hi Casey, we\'re looking into this now. Initial check shows no manual actions. We\'ll have a full update within 24 hours.',
            'is_internal' => false,
        ]);

        $ticket2 = Ticket::create([
            'tenant_id'   => $tenant->id,
            'client_id'   => $apex->id,
            'project_id'  => $project1->id,
            'assignee_id' => $agencyAdmin->id,
            'subject'     => 'Update meta descriptions on product category pages',
            'description' => 'All 14 product category pages need updated meta descriptions to match our new brand voice guidelines.',
            'type'        => 'task',
            'status'      => 'in_progress',
            'priority'    => 'medium',
        ]);

        $ticket3 = Ticket::create([
            'tenant_id'   => $tenant->id,
            'client_id'   => $meridian->id,
            'project_id'  => $project3->id,
            'assignee_id' => $agencyAdmin->id,
            'subject'     => 'Blog post #3 draft ready for review',
            'description' => 'Draft for "5 Signs You Need a Health Checkup" is ready. Please review and approve or provide feedback.',
            'type'        => 'question',
            'status'      => 'waiting',
            'priority'    => 'low',
        ]);

        $ticket4 = Ticket::create([
            'tenant_id'   => $tenant->id,
            'client_id'   => $apex->id,
            'assignee_id' => $agencyMember->id,
            'subject'     => 'Google Ads conversion tracking not firing',
            'description' => 'Purchase conversion event is not being tracked in Google Ads. Last recorded conversion was 3 days ago.',
            'type'        => 'bug',
            'status'      => 'resolved',
            'priority'    => 'high',
            'resolved_at' => now()->subDays(2),
        ]);

        // Deliverables
        Deliverable::create([
            'tenant_id'   => $tenant->id,
            'project_id'  => $project3->id,
            'client_id'   => $meridian->id,
            'name'        => 'Blog Post #1 — "Understanding Preventive Care"',
            'description' => 'Published and live on meridianhealth.com/blog',
            'status'      => 'approved',
            'submitted_at' => now()->subDays(30),
            'approved_at'  => now()->subDays(28),
        ]);

        Deliverable::create([
            'tenant_id'   => $tenant->id,
            'project_id'  => $project3->id,
            'client_id'   => $meridian->id,
            'name'        => 'Blog Post #2 — "Nutrition Basics for Busy Professionals"',
            'description' => 'Submitted for client review.',
            'status'      => 'in_review',
            'submitted_at' => now()->subDays(5),
            'due_date'     => now()->addDays(2),
        ]);

        Deliverable::create([
            'tenant_id'   => $tenant->id,
            'project_id'  => $project1->id,
            'client_id'   => $apex->id,
            'name'        => 'Q3 SEO Audit Report',
            'description' => 'Full technical SEO audit with 47 prioritized recommendations.',
            'status'      => 'approved',
            'submitted_at' => now()->subDays(20),
            'approved_at'  => now()->subDays(18),
        ]);
    }
}
