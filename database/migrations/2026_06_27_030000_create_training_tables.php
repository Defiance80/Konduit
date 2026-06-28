<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('general');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('estimated_minutes')->default(30);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('training_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->integer('duration_minutes')->default(5);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('training_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('training_lessons')->cascadeOnDelete();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });

        $this->seed();
    }

    public function down(): void
    {
        Schema::dropIfExists('training_completions');
        Schema::dropIfExists('training_lessons');
        Schema::dropIfExists('training_courses');
    }

    private function seed(): void
    {
        $now = now()->toDateTimeString();

        $courses = [
            [
                'title'       => 'Getting Started with Konduit',
                'description' => 'A complete walkthrough of the Konduit platform — from client setup to your first AI report. Perfect for new team members joining the agency.',
                'category'    => 'platform',
                'difficulty'  => 'beginner',
                'estimated_minutes' => 25,
                'sort_order'  => 1,
                'lessons' => [
                    [
                        'title'            => 'Platform Overview & Navigation',
                        'duration_minutes' => 8,
                        'sort_order'       => 1,
                        'content'          => '<h2>Welcome to Konduit</h2><p>Konduit is your agency\'s central intelligence platform. It brings together client management, project tracking, AI-powered reporting, and business forecasting into one unified workspace.</p><h3>Key Areas</h3><ul><li><strong>Dashboard</strong> — Your daily starting point. See active projects, open tickets, and your marketing intelligence brief.</li><li><strong>Operations</strong> — Clients, retainers, projects, tickets, tasks, and deliverables all live here.</li><li><strong>Intelligence</strong> — Capacity planning, audits, AI reports, messaging, relationship scores, and forecasting.</li><li><strong>Knowledge</strong> — Your SOP library, knowledge base, project blueprints, and training academy.</li><li><strong>Financial</strong> — Invoices and service packages.</li></ul><h3>Getting Around</h3><p>Use the left sidebar to navigate between sections. The sidebar groups are collapsible on smaller screens. Every record — client, project, ticket — has its own detail page where all related information is linked together.</p>',
                    ],
                    [
                        'title'            => 'Adding Your First Client',
                        'duration_minutes' => 7,
                        'sort_order'       => 2,
                        'content'          => '<h2>Creating a Client Record</h2><p>Every piece of work in Konduit connects back to a client. Setting up clients correctly from the start makes everything else faster.</p><h3>Step 1: Basic Info</h3><p>Go to <strong>Operations → Clients → Add Client</strong>. Fill in the client name, industry, website, and primary contact details. The industry field is used by the AI to generate relevant insights and news briefs.</p><h3>Step 2: Contact Person</h3><p>Add a primary contact name, email, and phone. This is the person the agency communicates with directly. You can add multiple contacts later from the client profile.</p><h3>Step 3: Retainer Range & Services</h3><p>Optionally set a retainer range and check the services the client is on. This feeds the Financial Intelligence and Capacity Engine.</p><h3>Step 4: Documents</h3><p>Upload contracts, NDAs, and onboarding docs directly to the client record under the Documents tab. These are stored securely and visible only to agency staff.</p><h3>Pro Tip</h3><p>Use the internal notes field for sensitive context (competitor relationships, personality notes, billing quirks). These are <strong>never visible to clients</strong> — they are strictly internal.</p>',
                    ],
                    [
                        'title'            => 'Creating Projects & Tickets',
                        'duration_minutes' => 10,
                        'sort_order'       => 3,
                        'content'          => '<h2>Projects vs Tickets</h2><p>Understanding when to use a project vs a ticket keeps work organised and traceable.</p><h3>Projects</h3><p>Projects are for multi-task, multi-week scopes of work — a website rebuild, a 3-month SEO campaign, a brand refresh. They have a timeline, a team, tasks, milestones, and deliverables.</p><ul><li>Go to <strong>Projects → New Project</strong></li><li>Assign it to a client and set a due date</li><li>Use the Kanban board to manage tasks inside the project</li><li>Deliverables are uploaded here for client approval</li></ul><h3>Tickets</h3><p>Tickets are for discrete, reactive requests — a bug fix, a quick content change, a support query. They\'re fast to create and track status through to resolution.</p><ul><li>Go to <strong>Tickets → New Ticket</strong></li><li>Set priority: Urgent, High, Medium, or Low</li><li>Assign to a team member and link to a client</li><li>Add internal comments to track progress without exposing them to the client</li></ul><h3>Deliverables & Approvals</h3><p>When a piece of work is ready for client sign-off, create a Deliverable. The client receives it in their portal under "Approvals" and can approve or request revisions.</p>',
                    ],
                ],
            ],
            [
                'title'       => 'Agency Operations Playbook',
                'description' => 'How to run an efficient digital marketing agency using Konduit\'s operational tools — retainers, capacity, SOPs, and client retention.',
                'category'    => 'agency_ops',
                'difficulty'  => 'intermediate',
                'estimated_minutes' => 40,
                'sort_order'  => 2,
                'lessons' => [
                    [
                        'title'            => 'Retainer Management Best Practices',
                        'duration_minutes' => 10,
                        'sort_order'       => 1,
                        'content'          => '<h2>Running Healthy Retainers</h2><p>Retainers are the lifeblood of a sustainable agency. How you manage them determines your profitability, team health, and client satisfaction.</p><h3>Setting Up a Retainer in Konduit</h3><p>Go to <strong>Operations → Retainers → New Retainer</strong>. Set the monthly value, hours included, and billing cycle. Link it to the client and set the start date.</p><h3>The 80/20 Rule for Retainer Hours</h3><p>Aim to complete 80% of retainer hours on planned, proactive work — content, campaigns, reports. The remaining 20% is your buffer for reactive requests and revisions. If you\'re consistently using over 100% of hours, it\'s a signal to have a scope conversation.</p><h3>Monitoring Usage</h3><p>The Retainer detail page shows hours consumed vs allocated. Set internal alerts at 75% and 90% to flag upcoming overages before they happen. Clients see a simplified view in their portal — no raw hour counts, just percentage consumed.</p><h3>Renewal Conversations</h3><p>Konduit\'s Relationship Intelligence scores each client on health indicators. Use these scores to identify clients due for a retainer review and prepare for renewal conversations with data, not gut feeling.</p>',
                    ],
                    [
                        'title'            => 'Using Capacity Engine for Team Planning',
                        'duration_minutes' => 12,
                        'sort_order'       => 2,
                        'content'          => '<h2>Capacity Planning</h2><p>The Capacity Engine gives you an 8-week forward view of your team\'s workload. Use it every Monday to plan the week and catch overloads before they become crises.</p><h3>Reading the Capacity Chart</h3><p>The chart shows tasks due per week as a percentage of theoretical team capacity (5 tasks per person per week as a baseline). Green is normal, amber is high, red is critical.</p><h3>Team Load View</h3><p>Below the chart, each team member\'s task count and load percentage is shown. If someone is over 80%, review their tasks and either redistribute or push out lower-priority items.</p><h3>The 8-Week Horizon</h3><p>Use weeks 5–8 for proactive planning. If you see a spike coming in week 6, start conversations now about which client deliverables can shift or where you need to add capacity.</p><h3>The Agency Simulator</h3><p>The Forecast Simulator lets you model scenarios: "What happens to capacity if we win 2 new clients?" or "What if we lose our largest retainer?" Use it quarterly for business planning.</p>',
                    ],
                    [
                        'title'            => 'SOPs, Knowledge Base & Blueprints',
                        'duration_minutes' => 8,
                        'sort_order'       => 3,
                        'content'          => '<h2>Institutional Knowledge in Konduit</h2><p>The fastest agencies are the ones that have documented processes and can onboard new team members without the founder being in every meeting.</p><h3>SOP Library</h3><p>Standard Operating Procedures are the step-by-step instructions for recurring tasks. Create SOPs for: client onboarding, content publishing workflows, monthly reporting, campaign launch checklists. Tag them by category and make them searchable.</p><h3>Knowledge Base</h3><p>The Knowledge Base is for reference information — brand guidelines, client-specific rules, tool documentation, meeting notes. Unlike SOPs, articles don\'t have to be step-by-step. They\'re a searchable wiki for the team.</p><h3>Project Blueprints</h3><p>Blueprints are project templates with predefined tasks, milestones, and sections. When you win a new website project, apply the "Website Rebuild" blueprint and the task list is pre-populated. This saves setup time and ensures consistency across similar projects.</p><h3>Keeping These Up To Date</h3><p>Assign an owner to each SOP. Set a review reminder every 6 months. Outdated SOPs are worse than no SOPs — they create false confidence. When you refine a process, update the SOP the same day.</p>',
                    ],
                    [
                        'title'            => 'Client Retention & Relationship Intelligence',
                        'duration_minutes' => 10,
                        'sort_order'       => 4,
                        'content'          => '<h2>Keeping Clients Longer</h2><p>The Relationship Intelligence module automatically calculates a health score for each client based on ticket volume, deliverable approvals, retainer usage, and communication frequency.</p><h3>Understanding the Health Score</h3><ul><li><strong>80–100 (Green)</strong> — Healthy relationship, renewal is likely. This is a good time to propose scope expansions.</li><li><strong>60–79 (Amber)</strong> — Watch carefully. Something is off — it could be slow delivery, unresolved tickets, or low engagement. Take proactive action.</li><li><strong>Below 60 (Red)</strong> — At-risk client. Schedule a relationship call this week. Don\'t wait for them to raise the issue.</li></ul><h3>Proactive Touch Points</h3><p>For any client below 70, add a proactive outreach task to your weekly list. It could be as simple as a quick win email, an early results report, or a 15-minute check-in call. Clients who feel seen are clients who stay.</p><h3>Using AI Summaries for Context</h3><p>Before any client call, generate an AI Summary of their account. It pulls together recent project activity, ticket history, retainer usage, and any AI insights into a concise brief. You\'ll walk in informed and confident.</p>',
                    ],
                ],
            ],
            [
                'title'       => 'Marketing Strategy Fundamentals',
                'description' => 'Core marketing strategy concepts every agency team member should know — from channel strategy to campaign fundamentals to measuring what matters.',
                'category'    => 'marketing',
                'difficulty'  => 'intermediate',
                'estimated_minutes' => 45,
                'sort_order'  => 3,
                'lessons' => [
                    [
                        'title'            => 'Channel Strategy: Where to Play',
                        'duration_minutes' => 12,
                        'sort_order'       => 1,
                        'content'          => '<h2>Choosing the Right Channels</h2><p>The most common mistake in digital marketing is trying to be everywhere. The best channel strategy is focused, not comprehensive.</p><h3>The Channel Selection Framework</h3><p>Ask three questions for every channel decision:</p><ol><li><strong>Where is the audience?</strong> — Match the channel to the buyer\'s demographics and behaviour. A B2B software buyer is on LinkedIn; a Gen Z fashion consumer is on TikTok.</li><li><strong>What stage of the funnel are we targeting?</strong> — Google Search is high intent (bottom of funnel). Instagram Stories are awareness (top of funnel). Mix channels intentionally.</li><li><strong>Can we execute consistently?</strong> — A channel you do brilliantly is worth more than three you do poorly. Don\'t recommend TikTok to a client who can\'t produce video content.</li></ol><h3>Organic vs Paid</h3><p>Organic (SEO, content, social) builds compounding long-term value but takes 3–6 months to show results. Paid (Google Ads, Meta Ads) delivers immediate traffic but stops the moment you turn off spend. Healthy strategy uses both, with paid funding the gap while organic builds.</p><h3>The Measurement Trap</h3><p>Don\'t let easy metrics drive strategy. Follower counts and impressions are vanity metrics. Focus on leads, pipeline, and revenue. Every campaign should connect to a business outcome.</p>',
                    ],
                    [
                        'title'            => 'SEO in 2026: What Actually Works',
                        'duration_minutes' => 14,
                        'sort_order'       => 2,
                        'content'          => '<h2>Modern SEO</h2><p>Search engine optimisation in 2026 is about authority, relevance, and user experience — not keyword density and backlink volume.</p><h3>E-E-A-T: The Non-Negotiable</h3><p>Google ranks content based on Experience, Expertise, Authoritativeness, and Trust. This means:</p><ul><li>Content should be written by or reviewed by subject matter experts</li><li>First-hand experience signals (case studies, proprietary data, examples) outperform generic advice</li><li>Author bylines with linked bios matter for YMYL (Your Money, Your Life) topics</li></ul><h3>AI Overviews & AEO</h3><p>Answer Engine Optimisation (AEO) is now as important as traditional SEO. AI Overviews pull from content that directly answers questions in the first 40–60 words. Structure content with: a direct answer up front, supporting detail below, clear headings, and FAQ sections.</p><h3>Technical Foundations</h3><p>Technical SEO is the floor, not the ceiling. Core Web Vitals (LCP, CLS, INP) must be healthy. A fast, crawlable, mobile-friendly site is the baseline assumption — not a differentiator. Use the Konduit Audit Engine to scan clients\' websites for technical issues.</p><h3>Schema Markup</h3><p>Structured data (FAQ, HowTo, Product, Review schema) increases the chance of rich results and AI feature inclusion. Every client should have at minimum: Organisation, LocalBusiness (if applicable), and FAQ schema where relevant.</p>',
                    ],
                    [
                        'title'            => 'Paid Advertising: ROI-First Approach',
                        'duration_minutes' => 10,
                        'sort_order'       => 3,
                        'content'          => '<h2>Running Paid Ads That Produce Results</h2><p>Paid advertising is not a magic button. It amplifies what already works. Before spending a dollar, make sure the offer, landing page, and tracking are right.</p><h3>The Paid Ads Checklist (Pre-Launch)</h3><ul><li>Conversion tracking is verified and firing correctly</li><li>Landing page is fast (&lt;3s load), mobile-optimised, and has a clear CTA</li><li>Offer is differentiated — why should someone click THIS ad?</li><li>Budget is realistic for the market — underspending in a competitive space gets no data</li><li>Attribution window is set correctly for the sales cycle</li></ul><h3>Google vs Meta: When to Use Each</h3><p><strong>Google Search</strong> captures existing demand — people already searching for your solution. High intent, higher CPC. Best for: local services, B2B SaaS, high-margin products.</p><p><strong>Meta Ads</strong> creates demand — interrupting people who aren\'t searching. Lower CPM, broader reach. Best for: e-commerce, consumer products, brand awareness, retargeting.</p><h3>The Optimisation Rhythm</h3><p>Week 1–2: Let the algorithm learn. Don\'t touch campaigns. Week 3–4: Review by audience segment and creative. Pause what\'s not working. Month 2+: Scale winners, test new angles. A good paid ads manager makes 80% of their money in the optimisation phase, not the launch.</p>',
                    ],
                    [
                        'title'            => 'Reporting to Clients: Telling the Story with Data',
                        'duration_minutes' => 9,
                        'sort_order'       => 4,
                        'content'          => '<h2>Client Reporting Done Right</h2><p>Reports are not for showing how busy you were. They\'re for proving you moved the needle and building confidence in the next month\'s investment.</p><h3>The 3-Part Report Structure</h3><ol><li><strong>Results This Period</strong> — Lead with wins. What key metrics improved? What goals were hit? Use specific numbers, not directional language.</li><li><strong>What We Did & Why</strong> — Briefly explain 2–3 key activities and the strategy behind them. This builds understanding, not just trust in data.</li><li><strong>Next Month\'s Focus</strong> — Show you have a plan. Clients who see clear direction don\'t second-guess the work.</li></ol><h3>Using Konduit AI Reports</h3><p>Use the AI Reports section to generate weekly, monthly, or end-of-year reports. The AI pulls from project data, deliverable history, and campaign context. You review and edit before sharing — the AI does the heavy lifting.</p><h3>The Language of Client Reports</h3><ul><li>Replace jargon: "Impressions" → "How many times your ad was shown"</li><li>Replace jargon: "CTR" → "What % of people clicked"</li><li>Replace jargon: "Bounce rate" → "Visitors who left without taking action"</li></ul><p>Clients who understand their reports are clients who trust their agency.</p>',
                    ],
                ],
            ],
            [
                'title'       => 'Client Communication & Account Management',
                'description' => 'How to build strong client relationships, handle difficult conversations, set expectations, and retain clients for the long term.',
                'category'    => 'client_mgmt',
                'difficulty'  => 'beginner',
                'estimated_minutes' => 30,
                'sort_order'  => 4,
                'lessons' => [
                    [
                        'title'            => 'Setting Expectations from Day One',
                        'duration_minutes' => 9,
                        'sort_order'       => 1,
                        'content'          => '<h2>Onboarding: The Foundation of Every Client Relationship</h2><p>The first 30 days of a client relationship determine whether they stay for 3 months or 3 years. Most agency problems start with poorly set expectations at onboarding.</p><h3>The Onboarding Call Agenda</h3><ol><li><strong>Goals review</strong> — Restate what success looks like in 90 days. Get explicit agreement.</li><li><strong>Communication cadence</strong> — When will you update them? What channel? How fast do you respond to messages?</li><li><strong>Their role</strong> — What do you need from them? Content approvals, asset access, feedback turnaround time.</li><li><strong>What\'s NOT in scope</strong> — Clarity here prevents the biggest source of client friction.</li></ol><h3>The Scope Document</h3><p>Before launching any project, send a written scope summary (email is fine). This becomes your reference when scope creep appears. It should list: deliverables, timeline, revision rounds, and what triggers additional charges.</p><h3>Access & Setup</h3><p>Collect everything you need access to at onboarding: Google Analytics, ad accounts, social profiles, CMS access, brand assets. Don\'t start work and then wait weeks for access. Make this a condition of kickoff.</p>',
                    ],
                    [
                        'title'            => 'Handling Difficult Client Conversations',
                        'duration_minutes' => 11,
                        'sort_order'       => 2,
                        'content'          => '<h2>Difficult Conversations Are How You Build Trust</h2><p>Most agencies avoid difficult conversations until they become expensive problems. The best account managers lean in early.</p><h3>The Three Common Scenarios</h3><p><strong>Scenario 1: Results are below expectations</strong><br>Don\'t wait for the client to raise it. Acknowledge it first, own what\'s in your control, and bring a plan. "I want to be upfront — this campaign underperformed. Here\'s why, and here\'s what we\'re changing this month."</p><p><strong>Scenario 2: Scope creep</strong><br>Don\'t silently absorb extra work. When a new request lands outside the agreed scope, respond within 24 hours: "Happy to take this on — this falls outside our current scope, so I\'ll send a quick scope addition for your approval." Then do it every time, without apology.</p><p><strong>Scenario 3: A client is angry</strong><br>Don\'t defend. Listen first. "I can see why that\'s frustrating — tell me more about the impact on your end." Only once they feel heard should you move to solutions. Angry clients want to be acknowledged before they want to be fixed.</p><h3>The 24-Hour Rule</h3><p>Respond to all client messages within 24 business hours — even if just to say "Got it, I\'m looking into this and will have an answer by Thursday." Silence breeds distrust faster than any mistake.</p>',
                    ],
                    [
                        'title'            => 'Using the Client Portal Effectively',
                        'duration_minutes' => 10,
                        'sort_order'       => 3,
                        'content'          => '<h2>The Client Portal as a Relationship Tool</h2><p>The Konduit client portal is more than a place for clients to check project status. Used well, it increases perceived value, reduces email volume, and gives clients the transparency they crave.</p><h3>What Clients See</h3><ul><li><strong>Dashboard</strong> — Project progress rings, retainer usage, open items needing their action</li><li><strong>Projects</strong> — Task completion progress, due dates, current phase</li><li><strong>Approvals</strong> — Deliverables waiting for their sign-off</li><li><strong>Reports</strong> — AI-generated summaries you\'ve marked as visible to them</li><li><strong>Support</strong> — Their tickets and history</li><li><strong>Retainer</strong> — Hours and budget consumed this cycle</li></ul><h3>What Clients Do NOT See</h3><p>Internal notes, team discussions, raw task details, financial margins, internal AI analysis, and anything not explicitly shared. This is by design — the portal shows clarity, not complexity.</p><h3>Making the Portal Sticky</h3><p>Train clients to use the portal instead of email for routine updates. At onboarding, walk them through their dashboard on a screenshare. Reference the portal in your weekly update: "Check your portal — I\'ve just uploaded this week\'s report." Clients who use the portal consistently are 40% less likely to churn.</p>',
                    ],
                ],
            ],
            [
                'title'       => 'AI Tools & Automation in Your Agency',
                'description' => 'How to leverage AI and automation to produce better client work in less time — from content generation to reporting to website audits.',
                'category'    => 'ai_tools',
                'difficulty'  => 'intermediate',
                'estimated_minutes' => 35,
                'sort_order'  => 5,
                'lessons' => [
                    [
                        'title'            => 'AI in the Agency Workflow',
                        'duration_minutes' => 10,
                        'sort_order'       => 1,
                        'content'          => '<h2>AI as a Force Multiplier</h2><p>AI doesn\'t replace good strategy or creative judgment. It eliminates the manual, repetitive work that eats hours without creating value. Used correctly, AI gives you more time to do the things only humans can do.</p><h3>Where AI Adds the Most Value</h3><ul><li><strong>First drafts</strong> — Briefs, copy, email sequences, social captions. AI produces a 70% draft; you elevate it to 100%.</li><li><strong>Data analysis</strong> — Summarising analytics data, spotting patterns in campaign performance, generating insight narratives.</li><li><strong>Client communication</strong> — Drafting update emails, report summaries, meeting agendas from notes.</li><li><strong>Research</strong> — Competitive analysis, industry overviews, audience persona drafts.</li></ul><h3>What AI Cannot Do</h3><ul><li>Understand your client\'s brand voice better than you do</li><li>Make judgment calls on strategy</li><li>Handle nuanced client relationships</li><li>Produce content with proprietary data, specific statistics, or real case studies without your input</li></ul><h3>The Golden Rule</h3><p>Never publish AI output without human review. AI hallucinates facts, misses brand nuance, and produces generic content without your guidance. Treat AI output as a smart first draft, not a finished product.</p>',
                    ],
                    [
                        'title'            => 'Using Konduit AI Features',
                        'duration_minutes' => 12,
                        'sort_order'       => 2,
                        'content'          => '<h2>Konduit\'s Built-In AI Capabilities</h2><p>Konduit integrates AI directly into your workflow so you don\'t need to copy-paste between tools.</p><h3>AI Summaries</h3><p>Generate AI summaries on any client or project page. The AI reads the project\'s tasks, deliverable history, ticket count, and retainer data to produce a concise account health brief. Use it before every client call. Mark it as visible to clients when appropriate for a shareable progress summary.</p><h3>AI Reports</h3><p>Under Intelligence → AI Reports, generate weekly, monthly, or end-of-year reports for clients. Set the report type, select the client, and the AI drafts the report. You review and edit, then mark it visible to share through the client portal.</p><h3>Website Audit Engine</h3><p>The Audit Engine can scan any website URL and produce a scored report across 6 dimensions: Technical SEO, Content & AEO, Schema, Performance, Social & Conversion, and Brand. The report includes a radar chart, priority recommendations, and issue-by-issue breakdowns. Use this for new client pitches or quarterly reviews.</p><h3>Marketing Intelligence Brief</h3><p>The News Feed section generates a daily AI-curated marketing intelligence brief tailored to your clients\' industries. Use it at the start of each day to stay current on algorithm changes, platform updates, and industry trends without spending an hour reading newsletters.</p>',
                    ],
                    [
                        'title'            => 'Prompt Engineering for Marketing',
                        'duration_minutes' => 13,
                        'sort_order'       => 3,
                        'content'          => '<h2>Getting Better Outputs from AI</h2><p>Prompt engineering is the skill of communicating with AI effectively. The quality of your output is directly proportional to the quality of your input.</p><h3>The SPEC Framework</h3><p>Use this structure for any marketing prompt:</p><ul><li><strong>S — Situation</strong>: Context about the brand, audience, and goal. "You are writing for [brand], a [type of business] targeting [audience]."</li><li><strong>P — Product/Service</strong>: What are we talking about? Include key features, differentiators, and proof points.</li><li><strong>E — Example</strong>: Provide a sample of the brand\'s existing voice or a reference you want to match.</li><li><strong>C — Constraints</strong>: Word count, tone, format, what to avoid, CTA required.</li></ul><h3>Common Prompts for Agency Work</h3><p><strong>Ad copy:</strong> "Write 5 variations of a 120-character Facebook ad headline for [Brand], targeting [audience]. The offer is [X]. Tone: [casual/professional/urgent]. Lead with the benefit, end with a CTA. Avoid jargon."</p><p><strong>Email subject lines:</strong> "Generate 10 email subject lines for a re-engagement campaign targeting [audience] who haven\'t purchased in 90 days. Include curiosity, urgency, and personalisation variations."</p><p><strong>SEO meta description:</strong> "Write a 155-character meta description for a page about [topic] targeting the keyword \'[keyword]\'. Include the primary benefit and a call to action. Don\'t use passive voice."</p><h3>The Review Checklist</h3><p>Before using any AI output: check facts, add specific brand details, ensure the voice matches, remove anything generic ("In today\'s fast-paced world..."), and verify claims are accurate.</p>',
                    ],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $lessons = $courseData['lessons'];
            unset($courseData['lessons']);

            $courseData['tenant_id']  = null;
            $courseData['is_published'] = true;
            $courseData['created_at'] = $now;
            $courseData['updated_at'] = $now;

            $courseId = DB::table('training_courses')->insertGetId($courseData);

            foreach ($lessons as $lesson) {
                $lesson['course_id']  = $courseId;
                $lesson['created_at'] = $now;
                $lesson['updated_at'] = $now;
                DB::table('training_lessons')->insert($lesson);
            }
        }
    }
};
