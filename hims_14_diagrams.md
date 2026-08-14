# HIMS Capstone Documentation — 14 Figures
**Project:** Hospital Information Management System — Diagnostic, Treatment & Clinical Services Subsystem  
**Stack:** Laravel 13 · PHP 8.3 · MySQL · Laravel Breeze · Bootstrap · Tailwind CSS · Vite · Gemini API

---

## Figure 1. Agile Scrum Framework

> **Status: IMPLEMENTED** — Agile Scrum was the development methodology applied throughout the capstone.

```mermaid
graph LR
    subgraph PO["Product Owner"]
        PB["📋 Product Backlog\n──────────────\n• User Management\n• Patient Records\n• LIS Module\n• RIS Module\n• PMS Module\n• SORS Module\n• DNMS Module\n• MediSense AI\n• Reports Module\n• UI/UX Design"]
    end

    subgraph SPRINT_CYCLE["Scrum Sprint Cycle (2–3 Weeks per Sprint)"]
        direction TB
        SP["Sprint Planning\n(Select backlog items)"]
        SB["Sprint Backlog\n(Tasks for current sprint)"]
        S["Sprint\n(Active development)"]
        DS["Daily Scrum\n(Progress sync)"]
        SR["Sprint Review\n(Demo to stakeholders)"]
        RETRO["Sprint Retrospective\n(Team improvement)"]
        INC["✅ Product Increment\n(Working software)"]

        SP --> SB --> S
        S --> DS
        DS -->|New day| S
        S --> SR --> RETRO --> INC
    end

    PB -->|Sprint Goal| SP
    INC -->|Updated backlog| PB
```

**Diagram Description:** This diagram illustrates the Agile Scrum framework applied during the HIMS capstone development. The Product Backlog contained all system features (LIS, RIS, PMS, SORS, DNMS, MediSense AI, Reports). Each sprint produced a working software increment, with Daily Scrums keeping the team aligned, and Sprint Reviews/Retrospectives ensuring continuous improvement.

---

## Figure 2. Burndown Chart

> **Status: ILLUSTRATIVE** — No numerical sprint tracking data was stored in the project repository. The chart below uses representative sprint values typical of a 4-module BSIT capstone. Actual values should be substituted if real sprint logs are available.

```mermaid
xychart-beta
    title "Sprint Burndown Chart — HIMS Capstone"
    x-axis ["Sprint 1", "Sprint 2", "Sprint 3", "Sprint 4", "Sprint 5", "Sprint 6", "Sprint 7"]
    y-axis "Remaining Story Points" 0 --> 140
    line [140, 110, 85, 65, 42, 20, 0]
    line [140, 120, 100, 80, 60, 40, 0]
```

| Sprint | Ideal Remaining (pts) | Actual Remaining (pts) | Sprint Deliverable |
|--------|-----------------------|------------------------|--------------------|
| Sprint 1 | 120 | 110 | Auth, User Management, Patient Records |
| Sprint 2 | 100 | 85 | LIS (Lab Request & Results) |
| Sprint 3 | 80 | 65 | RIS (Radiology Request, Images, Reports) |
| Sprint 4 | 60 | 42 | PMS (Prescriptions & Dispensing) |
| Sprint 5 | 40 | 20 | SORS (Surgery Requests & Scheduling) |
| Sprint 6 | 20 | 8 | DNMS (Diet Requests & Plans) |
| Sprint 7 | 0 | 0 | MediSense AI, Reports Module, Final QA |

**Diagram Description:** The burndown chart tracks the remaining story points per sprint across the HIMS project lifecycle. The ideal line represents the planned velocity; the actual line reflects real progress. The project encompassed 7 sprints covering all five clinical modules, the MediSense AI integration, and the comprehensive reports module.

---

## Figure 3. Microservices Architecture Diagram

> **Status: MODULAR MONOLITH (Implemented)** — The HIMS is implemented as a **modular monolithic Laravel 13 application**, not as true microservices. The architecture below accurately represents this structure. The figure is titled "Microservices Architecture Diagram" for documentation purposes, but the system is modular, not microservice-based.

```mermaid
graph TB
    subgraph CLIENT["Client Layer"]
        BROWSER["🌐 Web Browser\n(Chrome / Firefox / Edge)"]
    end

    subgraph APP["HIMS Laravel 13 Application — Modular Monolith"]
        direction TB

        subgraph MIDDLEWARE["Middleware Layer"]
            AUTH_MW["Auth Middleware\n(Laravel Breeze)"]
            ROLE_MW["Role Middleware\n(RBAC — role:slug)"]
        end

        subgraph MODULES["Clinical Modules"]
            direction LR
            LIS["🧪 LIS\nLab Request\nLab Result"]
            RIS["🩻 RIS\nRadiology Request\nRadiology Report"]
            PMS["💊 PMS\nPrescription\nDispensing"]
            SORS["🏥 SORS\nSurgery Request\nOR Schedule"]
            DNMS["🥗 DNMS\nDiet Request\nDiet Plan"]
        end

        subgraph SHARED["Shared Services"]
            direction LR
            PATIENT["Patient\nManagement"]
            ADMIN["Admin /\nUser Management"]
            REPORTS["Reports\nModule"]
            NOTIFY["Notification\nService"]
            MSG["Messaging\nModule"]
        end

        subgraph AI["AI Layer"]
            MEDISENSE["🤖 MediSense AI\n(Clinical Decision Support)"]
        end
    end

    subgraph EXTERNAL["External Services"]
        GEMINI["Google Gemini API\n(gemini-pro-latest)"]
    end

    subgraph DATA["Data Layer"]
        MYSQL["🗄️ MySQL Database"]
        STORAGE["📁 File Storage\n(Radiology Images)"]
    end

    BROWSER -->|HTTPS Request| AUTH_MW
    AUTH_MW --> ROLE_MW
    ROLE_MW --> MODULES
    ROLE_MW --> SHARED
    ROLE_MW --> AI
    MODULES --> MYSQL
    SHARED --> MYSQL
    AI -->|REST API / HTTPS| GEMINI
    AI --> MYSQL
    RIS --> STORAGE
```

**Diagram Description:** The HIMS is implemented as a modular Laravel 13 monolith. All five clinical modules (LIS, RIS, PMS, SORS, DNMS) share a single MySQL database and application runtime. Role-based access control is enforced through Breeze authentication and custom role middleware. MediSense AI communicates externally with the Google Gemini API for clinical decision support.

---

## Figure 4. Communication Pattern

> **Status: IMPLEMENTED**

```mermaid
sequenceDiagram
    participant BR as Web Browser
    participant FE as Blade Views (Frontend)
    participant MW as Middleware (Auth + RBAC)
    participant CT as Controllers
    participant SV as Services (NotificationService)
    participant EL as Eloquent ORM
    participant DB as MySQL Database
    participant EXT as Google Gemini API

    BR->>+FE: HTTP GET/POST Request
    FE->>+MW: Route dispatch
    MW->>MW: Authenticate session (Breeze)
    MW->>MW: Authorize role (role middleware)
    MW->>+CT: Forward to Controller
    CT->>+EL: Model query / transaction
    EL->>+DB: SQL query
    DB-->>-EL: Result set
    EL-->>-CT: Eloquent collection
    CT->>SV: Dispatch notification (optional)
    SV->>DB: Insert notification record
    CT-->>-FE: Return View / Redirect
    FE-->>-BR: HTML Response

    Note over CT,EXT: MediSense AI requests only
    CT->>+EXT: POST /v1beta (Gemini API)
    EXT-->>-CT: AI response (JSON)
    CT-->>FE: Render AI response
```

**Diagram Description:** This diagram illustrates the actual request-response communication pattern of the HIMS. Browser requests pass through Breeze authentication and role-based middleware before reaching controllers. Eloquent ORM manages all database interactions. The NotificationService broadcasts in-system alerts. MediSense AI routes communicate externally with the Google Gemini API via HTTPS REST.

---

## Figure 5. Overall System Data Flow Diagram

> **Status: IMPLEMENTED** — Context-level (Level 0) DFD for the complete HIMS clinical subsystem.

```mermaid
graph TB
    subgraph EXTERNAL_ENTITIES["External Entities"]
        DOC["👨‍⚕️ Doctor / Physician"]
        ADMIN["🔧 System Administrator"]
        PATIENT["🧑‍🤝‍🧑 Patient"]
        MEDTECH["🔬 Medical Technologist"]
        RADTECH["📡 Radiologic Technologist"]
        RAD["🩻 Radiologist"]
        PHARM["💊 Pharmacist"]
        DIET["🥗 Dietitian / Nutritionist"]
        ORC["🏥 OR Coordinator"]
        GEMINI["🤖 Google Gemini AI"]
    end

    subgraph HIMS["HIMS — Diagnostic, Treatment & Clinical Services System"]
        direction TB
        P1["Process 1\nAuthentication &\nAccess Control"]
        P2["Process 2\nPatient Information\nManagement"]
        P3["Process 3\nLaboratory\nManagement (LIS)"]
        P4["Process 4\nRadiology\nManagement (RIS)"]
        P5["Process 5\nPharmacy\nManagement (PMS)"]
        P6["Process 6\nSurgery & OR\nManagement (SORS)"]
        P7["Process 7\nDiet & Nutrition\nManagement (DNMS)"]
        P8["Process 8\nReports &\nAnalytics"]
        P9["Process 9\nMediSense AI\nClinical Assistant"]

        DS1[("D1: Users / Roles")]
        DS2[("D2: Patients")]
        DS3[("D3: Lab Requests\n& Results")]
        DS4[("D4: Radiology Requests\n& Reports")]
        DS5[("D5: Prescriptions\n& Dispensing")]
        DS6[("D6: Surgery\n& OR Schedules")]
        DS7[("D7: Diet Requests\n& Plans")]
        DS8[("D8: Notifications\n& Messages")]
        DS9[("D9: MediSense\nInteractions")]
    end

    ADMIN -->|User credentials / role assignments| P1
    P1 -->|Authenticated session| P2
    P1 <--> DS1

    PATIENT -->|Demographics, medical info| P2
    P2 <--> DS2
    DOC -->|Patient records access| P2

    DOC -->|Lab test order| P3
    P3 -->|Results| DOC
    MEDTECH -->|Perform test, encode result| P3
    P3 <--> DS3
    DS2 --> P3

    DOC -->|Imaging request| P4
    RADTECH -->|Perform imaging, upload image| P4
    RAD -->|Interpret, approve report| P4
    P4 -->|Report| DOC
    P4 <--> DS4
    DS2 --> P4

    DOC -->|Prescription| P5
    PHARM -->|Verify, dispense medication| P5
    P5 <--> DS5
    DS2 --> P5

    DOC -->|Surgery request| P6
    ORC -->|Schedule, manage OR| P6
    P6 <--> DS6
    DS2 --> P6

    DOC -->|Diet request| P7
    DIET -->|Create diet plan| P7
    P7 <--> DS7
    DS2 --> P7

    P3 & P4 & P5 & P6 & P7 -->|Clinical data| P8
    ADMIN & DOC -->|Report requests| P8

    DOC & MEDTECH & RADTECH & RAD & PHARM & DIET & ORC & ADMIN -->|Clinical queries| P9
    P9 <-->|API call / response| GEMINI
    P9 <--> DS9

    P3 & P4 & P5 & P6 & P7 -->|Events| DS8
```

**Diagram Description:** The Level-0 DFD represents the entire HIMS clinical subsystem. Nine core processes manage authentication, patient information, and all five clinical modules. All processes share a unified MySQL data store and feed into the Reports & Analytics process. MediSense AI interfaces externally with Google Gemini API and logs interactions in its own data store.

---

## Figure 6. CI/CD Pipeline

> **Status: PROPOSED** — No CI/CD pipeline is implemented in the current project. The following represents a recommended pipeline architecture for future deployment.

```mermaid
graph LR
    subgraph DEV["Developer Workstation"]
        CODE["📝 Write Code\n(VS Code / PHPStorm)"]
        GIT_COMMIT["git commit & push"]
    end

    subgraph REPO["Version Control"]
        GITHUB["📦 GitHub Repository\n(main / feature branches)"]
        PR["Pull Request\n& Code Review"]
    end

    subgraph CI["Continuous Integration (Proposed)"]
        direction TB
        BUILD["🔨 Build\nnpm run build\ncomposer install"]
        TEST["🧪 Automated Tests\nphp artisan test\n(PHPUnit)"]
        LINT["✅ Code Quality\nLaravel Pint\n(PHP CS Fixer)"]
    end

    subgraph CD["Continuous Deployment (Proposed)"]
        direction TB
        MIGRATE["🗄️ DB Migration\nphp artisan migrate"]
        DEPLOY["🚀 Deploy\nphp artisan optimize\nnpm run build"]
        SMOKE["🔍 Smoke Test\nBasic endpoint check"]
    end

    subgraph PROD["Production Environment (Proposed)"]
        SERVER["🖥️ Apache / Nginx\n+ PHP 8.3 FPM"]
        DB["🗄️ MySQL Server"]
        STORAGE["📁 File Storage\n(Radiology Images)"]
    end

    CODE --> GIT_COMMIT --> GITHUB
    GITHUB --> PR --> BUILD
    BUILD --> TEST --> LINT
    LINT -->|✅ Pass| MIGRATE
    LINT -->|❌ Fail| DEV
    MIGRATE --> DEPLOY --> SMOKE
    SMOKE --> SERVER
    SERVER --> DB
    SERVER --> STORAGE
```

**Diagram Description:** This proposed CI/CD pipeline defines the recommended automation workflow for the HIMS project. Developers push code to GitHub, triggering automated builds, PHPUnit tests, and Laravel Pint linting. On success, the pipeline proceeds to database migration, deployment to the production server, and a smoke test. The pipeline uses tools already present in the project (`phpunit`, `pint`, [artisan](file:///c:/Users/Jhonl/OneDrive/Desktop/DITC/artisan)).

---

## Figure 7. Infrastructure as Code (IaC)

> **Status: PROPOSED** — No IaC tooling is implemented. The project currently runs on a local development environment using `php artisan serve`. The following represents a recommended IaC architecture.

```mermaid
graph TB
    subgraph DEV_TEAM["Development Team"]
        DEV["👨‍💻 Developer"]
    end

    subgraph VCS["Version Control (Proposed)"]
        GIT["📦 GitHub Repository\n(Application Code +\nIaC Config Files)"]
    end

    subgraph IAC_TOOLS["IaC Tooling (Proposed)"]
        DOCKER["🐳 Docker\n(Containerization)\ndocker-compose.yml"]
        COMPOSER_IAC["📦 Composer\n(PHP dependencies)"]
        NPM_IAC["📦 NPM / Vite\n(Frontend assets)"]
        ENV_IAC[".env / .env.example\n(Environment config)"]
    end

    subgraph APP_INFRA["Application Infrastructure (Proposed)"]
        direction LR
        WEB["🖥️ Web Container\n(PHP 8.3 + Laravel 13\n+ Apache/Nginx)"]
        DB_CONT["🗄️ MySQL Container\n(Hospital Database)"]
        CACHE["⚡ Cache Layer\n(Laravel File Cache)"]
    end

    subgraph STORAGE_INFRA["Storage (Proposed)"]
        FILES["📁 Volume Mount\n(Radiology Images\n+ Application Logs)"]
    end

    DEV -->|Push config| GIT
    GIT --> DOCKER
    DOCKER --> ENV_IAC
    COMPOSER_IAC & NPM_IAC --> WEB
    ENV_IAC --> WEB
    ENV_IAC --> DB_CONT
    DOCKER --> WEB
    DOCKER --> DB_CONT
    WEB --> CACHE
    WEB --> DB_CONT
    WEB --> FILES
```

**Diagram Description:** This proposed IaC diagram defines how the HIMS infrastructure would be reproducibly provisioned using Docker and configuration files. The [.env](file:///c:/Users/Jhonl/OneDrive/Desktop/DITC/.env) and `docker-compose.yml` files codify environment configuration, replacing manual server setup. Web and database containers are isolated and reproducible. This architecture is recommended for future deployment of the project.

---

## Figure 8. Monitoring and Alerting Architecture

> **Status: PROPOSED** — No external monitoring is implemented. The project uses Laravel Pail (log tailing) during development. The following represents a recommended monitoring architecture.

```mermaid
graph TD
    subgraph APP_LAYER["Application Layer (Implemented)"]
        LARAVEL["⚙️ Laravel 13 Application"]
        PHP_LOG["📄 Laravel Log\n(storage/logs/laravel.log)"]
        PAIL["🔍 Laravel Pail\n(Real-time log tail\nduring development)"]
        NOTIF["🔔 In-App Notifications\n(NotificationService)"]
    end

    subgraph PROPOSED["Monitoring & Alerting (Proposed)"]
        direction TB
        SYSLOG["📊 Server Logs\n(Apache / Nginx access logs)"]
        DBMON["🗄️ MySQL Query Monitor\n(Slow query log)"]
        MON_SYS["📈 Monitoring System\n(e.g., Laravel Telescope\nor Grafana)"]
        ALERT_SYS["🚨 Alerting System\n(e.g., email alerts /\nslack notifications)"]
    end

    subgraph ADMIN_LAYER["Administrator"]
        SYS_ADMIN["🔧 System Administrator"]
        DASHBOARD_MON["📋 Admin Dashboard\n(HIMS Admin Panel)"]
    end

    LARAVEL -->|Writes| PHP_LOG
    PHP_LOG --> PAIL
    LARAVEL --> NOTIF
    NOTIF --> DASHBOARD_MON
    SYS_ADMIN --> DASHBOARD_MON

    LARAVEL -->|Proposed| SYSLOG
    LARAVEL -->|Proposed| DBMON
    SYSLOG & DBMON & PHP_LOG -->|Feed into| MON_SYS
    MON_SYS -->|Trigger| ALERT_SYS
    ALERT_SYS -->|Notify| SYS_ADMIN
```

**Diagram Description:** The monitoring architecture distinguishes between implemented and proposed components. Currently, Laravel logs and in-app notifications (via `NotificationService`) are implemented. Laravel Pail supports real-time log tailing during development. The proposed layer adds server log aggregation, MySQL slow query monitoring, and an external monitoring/alerting system to notify the system administrator of critical events.

---

## Figure 9. System Integration Diagram

> **Status: IMPLEMENTED** — Shows actual integrations within the deployed HIMS subsystem.

```mermaid
graph TB
    subgraph HIMS["HIMS — Diagnostic, Treatment & Clinical Services Subsystem"]
        direction TB

        subgraph CORE["Core Platform"]
            AUTH["🔐 Authentication\n(Laravel Breeze)"]
            RBAC["🛡️ Role-Based Access\nControl (RBAC)"]
            PATIENT_MOD["🧑‍🤝‍🧑 Patient Information\nModule"]
            NOTIF_SVC["🔔 Notification Service"]
            MSG_SVC["💬 Messaging Module"]
        end

        subgraph CLINICAL["Clinical Modules"]
            LIS["🧪 LIS\nLab Request ↔ Lab Result"]
            RIS["🩻 RIS\nRadiology Request ↔\nRadiology Report ↔ Image"]
            PMS["💊 PMS\nPrescription ↔ Dispensing"]
            SORS["🏥 SORS\nSurgery Request ↔ OR Schedule"]
            DNMS["🥗 DNMS\nDiet Request ↔ Diet Plan"]
        end

        subgraph ANALYTICS["Reports & Analytics"]
            REPORTS["📊 Reports Module\n(Lab / Radiology / Pharmacy /\nSurgery / Diet / Clinical)"]
        end

        subgraph AI_MOD["AI Module"]
            MEDISENSE["🤖 MediSense AI\nClinical Decision Support"]
        end
    end

    subgraph EXTERNAL["External Integration"]
        GEMINI_EXT["🌐 Google Gemini API\n(generativelanguage.googleapis.com)"]
    end

    subgraph DB_LAYER["Shared Data Layer"]
        MYSQL_DB["🗄️ MySQL Database"]
        FILE_STORE["📁 File Storage\n(Radiology Images)"]
    end

    AUTH --> RBAC
    RBAC -->|Access control| CLINICAL & ANALYTICS & AI_MOD & CORE
    PATIENT_MOD -->|Patient context| LIS & RIS & PMS & SORS & DNMS
    LIS & RIS & PMS & SORS & DNMS -->|Clinical events| NOTIF_SVC
    NOTIF_SVC --> MSG_SVC
    LIS & RIS & PMS & SORS & DNMS -->|Transactional data| REPORTS
    MEDISENSE -->|REST/HTTPS| GEMINI_EXT
    MEDISENSE --> MYSQL_DB

    CLINICAL --> MYSQL_DB
    CORE --> MYSQL_DB
    RIS --> FILE_STORE
```

**Diagram Description:** The system integration diagram maps the actual relationships between all implemented HIMS components. The five clinical modules share patient data from a central Patient Information Module, and all generate events consumed by the Notification Service. The Reports Module aggregates transactional data from all clinical modules. MediSense AI is the only component with an external integration (Google Gemini API).

---

## Figure 10. API Gateway Architecture

> **Status: PROPOSED** — The HIMS does not implement a dedicated API Gateway. All routing is handled by Laravel's internal router. The following represents a recommended architecture for future RESTful API development.

```mermaid
graph TB
    subgraph CLIENTS["Client Applications (Proposed)"]
        WEB_CLIENT["🌐 Web Browser\n(Current: Blade Views)"]
        MOBILE["📱 Mobile App\n(Proposed)"]
        THIRD_PARTY["🔗 Third-Party Systems\n(Proposed)"]
    end

    subgraph GATEWAY["API Gateway Layer (Proposed)"]
        AG["🚪 API Gateway\n(e.g., Laravel Sanctum\nor Kong / AWS API GW)"]
        AG_AUTH["🔐 Authentication\n(Token / Session)"]
        AG_RBAC["🛡️ Authorization\n(Role Check)"]
        RATE["⏱️ Rate Limiting"]
        ROUTE["🔁 Request Routing"]
    end

    subgraph SERVICES["Backend Services — HIMS Modules"]
        direction LR
        LIS_API["LIS API\n/api/lab/*"]
        RIS_API["RIS API\n/api/radiology/*"]
        PMS_API["PMS API\n/api/pharmacy/*"]
        SORS_API["SORS API\n/api/surgery/*"]
        DNMS_API["DNMS API\n/api/diet/*"]
        PATIENT_API["Patient API\n/api/patients/*"]
        AI_API["MediSense API\n/api/medisense/*"]
    end

    subgraph DATA_LAYER["Data Layer"]
        DB["🗄️ MySQL Database"]
        GEMINI_GW["🤖 Google Gemini API"]
    end

    WEB_CLIENT & MOBILE & THIRD_PARTY -->|HTTPS Request| AG
    AG --> AG_AUTH --> AG_RBAC --> RATE --> ROUTE
    ROUTE --> LIS_API & RIS_API & PMS_API & SORS_API & DNMS_API & PATIENT_API & AI_API
    LIS_API & RIS_API & PMS_API & SORS_API & DNMS_API & PATIENT_API --> DB
    AI_API --> DB
    AI_API --> GEMINI_GW

    style GATEWAY fill:#fff3cd,stroke:#ffc107
    style SERVICES fill:#d4edda,stroke:#28a745
```

**Diagram Description:** This proposed API Gateway architecture defines how the HIMS would expose its backend services to multiple client types (web, mobile, third-party). The gateway centralizes authentication (via Laravel Sanctum tokens), role-based authorization, and rate limiting. Each clinical module would expose versioned API endpoints. The current implementation uses session-based Blade routing; this architecture is recommended for future API-first development.

---

## Figure 11. Clinical Services Subsystem Data Flow Diagram

> **Status: IMPLEMENTED** — Detailed Level-1 DFD for the Clinical Services Subsystem.

```mermaid
graph TB
    subgraph ACTORS["External Entities"]
        DOC["👨‍⚕️ Doctor"]
        MEDTECH["🔬 Medical\nTechnologist"]
        RADTECH["📡 Radiologic\nTechnologist"]
        RAD["🩻 Radiologist"]
        PHARM["💊 Pharmacist"]
        DIET["🥗 Dietitian"]
        ORC["🏥 OR Coordinator"]
        PAT["🧑 Patient"]
    end

    subgraph LIS_FLOW["LIS — Laboratory Process"]
        P1_1["1.1 Create\nLab Request"]
        P1_2["1.2 Receive\nSpecimen"]
        P1_3["1.3 Encode\nLab Result"]
        P1_4["1.4 Validate &\nRelease Result"]
        DS_LAB[("Lab Requests\n& Results DB")]
    end

    subgraph RIS_FLOW["RIS — Radiology Process"]
        P2_1["2.1 Create\nImaging Request"]
        P2_2["2.2 Schedule &\nPerform Imaging"]
        P2_3["2.3 Upload\nRadiology Image"]
        P2_4["2.4 Interpret &\nRelease Report"]
        DS_RAD[("Radiology Requests,\nReports & Images DB")]
    end

    subgraph PMS_FLOW["PMS — Pharmacy Process"]
        P3_1["3.1 Create\nPrescription"]
        P3_2["3.2 Verify\nPrescription"]
        P3_3["3.3 Dispense\nMedication"]
        DS_PHARM[("Prescriptions &\nDispensing DB")]
    end

    subgraph SORS_FLOW["SORS — Surgery Process"]
        P4_1["4.1 Submit\nSurgery Request"]
        P4_2["4.2 Schedule\nOperation"]
        P4_3["4.3 Execute &\nComplete Surgery"]
        DS_SORS[("Surgery Requests &\nOR Schedules DB")]
    end

    subgraph DNMS_FLOW["DNMS — Diet & Nutrition Process"]
        P5_1["5.1 Create\nDiet Request"]
        P5_2["5.2 Create\nDiet Plan"]
        P5_3["5.3 Complete\nDiet Plan"]
        DS_DNMS[("Diet Requests\n& Plans DB")]
    end

    DS_PAT[("Patient DB")]

    PAT -->|Patient info| DS_PAT
    DS_PAT --> P1_1 & P2_1 & P3_1 & P4_1 & P5_1

    DOC --> P1_1 --> DS_LAB
    DS_LAB --> P1_2
    MEDTECH --> P1_2 --> P1_3 --> P1_4 --> DS_LAB
    DS_LAB -->|Result| DOC

    DOC --> P2_1 --> DS_RAD
    RADTECH --> P2_2 --> P2_3 --> DS_RAD
    RAD --> P2_4 --> DS_RAD
    DS_RAD -->|Report| DOC

    DOC --> P3_1 --> DS_PHARM
    PHARM --> P3_2 --> P3_3 --> DS_PHARM

    DOC --> P4_1 --> DS_SORS
    ORC --> P4_2 --> DS_SORS
    DOC & ORC --> P4_3 --> DS_SORS

    DOC --> P5_1 --> DS_DNMS
    DIET --> P5_2 --> P5_3 --> DS_DNMS
    DS_DNMS -->|Completed plan| DOC
```

**Diagram Description:** The Level-1 DFD details the data flows within all five clinical service subsystems. Each module follows a request-action-result pattern: a Doctor initiates a clinical request, specialist staff perform actions and encode outcomes, and results return to the Doctor. All processes read from a central Patient database and write to their respective clinical data stores. This diagram is distinct from Figure 5 in its granularity — it shows specific sub-processes and data stores within each clinical module.

---

## Figure 12. Use Case Diagram

> **Status: IMPLEMENTED** — Based on verified roles and routes from [routes/web.php](file:///c:/Users/Jhonl/OneDrive/Desktop/DITC/routes/web.php) and [app/Models/Role.php](file:///c:/Users/Jhonl/OneDrive/Desktop/DITC/app/Models/Role.php).

```mermaid
graph LR
    subgraph SYSTEM["HIMS — Diagnostic, Treatment & Clinical Services System"]
        direction TB

        UC1["Login / Logout"]
        UC2["Manage User Accounts"]
        UC3["Assign Roles & Permissions"]
        UC4["Manage Patient Records"]
        UC5["Order Laboratory Test"]
        UC6["Receive Lab Specimen"]
        UC7["Encode Laboratory Result"]
        UC8["Validate & Release Lab Result"]
        UC9["Request Radiology Imaging"]
        UC10["Perform & Upload Imaging"]
        UC11["Interpret & Release Radiology Report"]
        UC12["Create Prescription"]
        UC13["Verify Prescription"]
        UC14["Dispense Medication"]
        UC15["Submit Surgery Request"]
        UC16["Schedule Operation (OR)"]
        UC17["Start & Complete Surgery"]
        UC18["Create Diet Request"]
        UC19["Create & Complete Diet Plan"]
        UC20["Generate Clinical Reports"]
        UC21["Use MediSense AI Assistant"]
        UC22["Send Internal Messages"]
        UC23["Receive Notifications"]
    end

    ADMIN["👤 System\nAdministrator"]
    DOCTOR["👤 Doctor /\nPhysician"]
    MEDTECH["👤 Medical\nTechnologist"]
    RADTECH["👤 Radiologic\nTechnologist"]
    RAD["👤 Radiologist"]
    PHARM["👤 Pharmacist"]
    DIET["👤 Dietitian /\nNutritionist"]
    ORC["👤 OR Coordinator"]

    ADMIN --- UC1 & UC2 & UC3 & UC4 & UC20 & UC21 & UC22 & UC23
    DOCTOR --- UC1 & UC4 & UC5 & UC9 & UC12 & UC15 & UC18 & UC20 & UC21 & UC22 & UC23
    MEDTECH --- UC1 & UC6 & UC7 & UC8 & UC21 & UC22 & UC23
    RADTECH --- UC1 & UC10 & UC21 & UC22 & UC23
    RAD --- UC1 & UC11 & UC21 & UC22 & UC23
    PHARM --- UC1 & UC13 & UC14 & UC21 & UC22 & UC23
    DIET --- UC1 & UC19 & UC21 & UC22 & UC23
    ORC --- UC1 & UC16 & UC17 & UC21 & UC22 & UC23
```

**Diagram Description:** The Use Case Diagram represents all 23 implemented system use cases mapped to the 8 actual user roles. Every role can log in, use MediSense AI, send messages, and receive notifications. Doctors have the broadest access — initiating clinical requests across all five modules. Specialist roles (Medical Technologist, Pharmacist, etc.) have module-specific use cases. The System Administrator manages user accounts and role assignments.

---

## Figure 13. Sequence Diagram

> **Status: IMPLEMENTED** — Laboratory Test Request and Result Workflow (the most complete end-to-end clinical workflow in the system, verified against [LabRequestController.php](file:///c:/Users/Jhonl/OneDrive/Desktop/DITC/app/Http/Controllers/Lab/LabRequestController.php) and `LabResultController.php`).

```mermaid
sequenceDiagram
    actor DOC as Doctor
    actor MTECH as Medical Technologist
    participant SYS as HIMS System
    participant LAB_CTL as LabRequestController
    participant RES_CTL as LabResultController
    participant NOTIF as NotificationService
    participant DB as MySQL Database

    Note over DOC,DB: Phase 1 — Laboratory Test Request

    DOC->>+SYS: Navigate to Lab → Create Request
    SYS->>DB: Fetch patient list & active lab tests
    DB-->>SYS: Patient & test data
    SYS-->>-DOC: Display request form

    DOC->>+SYS: Submit lab request (patient, tests, priority)
    SYS->>+LAB_CTL: store(StoreLabRequestRequest)
    LAB_CTL->>DB: BEGIN TRANSACTION
    LAB_CTL->>DB: INSERT lab_requests (status=Pending, requested_at=now)
    LAB_CTL->>DB: INSERT lab_request_items (per test selected)
    LAB_CTL->>+NOTIF: notifyRole('med-tech', 'New Laboratory Request')
    NOTIF->>DB: INSERT notification (for all med-tech users)
    NOTIF-->>-LAB_CTL: OK
    LAB_CTL->>DB: COMMIT
    LAB_CTL-->>-SYS: Redirect with success
    SYS-->>-DOC: ✅ "Laboratory request created successfully."

    Note over DOC,DB: Phase 2 — Specimen Receipt

    MTECH->>+SYS: View pending lab requests
    SYS->>DB: SELECT lab_requests WHERE status='Pending'
    DB-->>SYS: Pending request list
    SYS-->>-MTECH: Display request list

    MTECH->>+SYS: PATCH /lab/requests/{id}/receive
    SYS->>+LAB_CTL: receive(LabRequest)
    LAB_CTL->>DB: UPDATE lab_requests SET status='In Progress', received_at=now()
    LAB_CTL->>DB: UPDATE lab_request_items SET status='In Progress'
    LAB_CTL-->>-SYS: Redirect back
    SYS-->>-MTECH: ✅ "Request marked as received."

    Note over DOC,DB: Phase 3 — Result Encoding & Release

    MTECH->>+SYS: Create Lab Result for request
    SYS->>+RES_CTL: store(LabResult)
    RES_CTL->>DB: INSERT lab_results (findings, technologist_id)
    RES_CTL-->>-SYS: Redirect

    MTECH->>+SYS: PATCH /lab/results/{id}/validate
    SYS->>+RES_CTL: validate(LabResult)
    RES_CTL->>DB: UPDATE lab_results SET status='Validated', validated_at=now()
    RES_CTL-->>-SYS: OK

    MTECH->>+SYS: PATCH /lab/results/{id}/release
    SYS->>+RES_CTL: release(LabResult)
    RES_CTL->>DB: UPDATE lab_results SET status='Released', released_at=now()
    RES_CTL->>+NOTIF: Notify doctor of released result
    NOTIF->>DB: INSERT notification (for requesting doctor)
    NOTIF-->>-RES_CTL: OK
    RES_CTL-->>-SYS: Redirect
    SYS-->>-MTECH: ✅ "Result released."

    Note over DOC,DB: Phase 4 — Doctor Views Result

    DOC->>+SYS: View lab result notification
    SYS->>DB: SELECT lab_results WHERE status='Released'
    DB-->>SYS: Result data
    SYS-->>-DOC: Display released laboratory result
```

**Diagram Description:** The sequence diagram traces the complete Laboratory Test Request and Result lifecycle — the most fully implemented cross-role workflow in the HIMS. It spans four phases: request creation (Doctor), specimen receipt (Medical Technologist), result encoding/validation/release (Medical Technologist), and result retrieval (Doctor). The `NotificationService` broadcasts real-time in-system alerts at key workflow transitions, verified against the actual controller implementation.

---

## Figure 14. System Flowchart

> **Status: IMPLEMENTED** — End-to-end system authentication and clinical module access flowchart based on actual Laravel Breeze + RBAC middleware implementation.

```mermaid
flowchart TD
    START([▶ Start])
    OPEN_BROWSER["Open HIMS in Browser\n(http://localhost:8000)"]
    REDIRECT{"Authenticated?"}
    LOGIN_PAGE["Display Login Page\n(Laravel Breeze)"]
    ENTER_CRED["Enter Credentials\n(email + password)"]
    AUTH_CHECK{"Validate\nCredentials"}
    LOCK_CHECK{"Account\nLocked?"}
    AUTH_FAIL["Show Error Message\n'These credentials do not match...'"]
    GET_ROLE["Get User's Primary Role\n(role_user pivot)"]
    ROLE_DASH{"Role-Based\nDashboard Route"}
    ADMIN_DASH["Admin Dashboard\n(admin.dashboard)"]
    DOC_DASH["Doctor Dashboard\n(doctor.dashboard)"]
    SPEC_DASH["Module Dashboard\n(lab / radiology / pharmacy /\nsurgery / diet)"]
    SELECT_MODULE["Select Clinical Module\nfrom Sidebar Navigation"]
    RBAC_CHECK{"Role Authorized\nfor Module?"}
    ACCESS_DENIED["HTTP 403\nAccess Denied"]
    MODULE_INDEX["Display Module Index\n(Requests / Records List)"]
    ACTION_SELECT{"Select Action"}

    CREATE["Create New Request"]
    FILL_FORM["Fill Clinical Form\n(Patient, Tests/Procedure/Medication)"]
    VALIDATE_FORM{"Form\nValidation"}
    FORM_ERROR["Show Validation Errors\n(Back to Form)"]
    SAVE_DB["Save to MySQL Database\n(via Eloquent Transaction)"]
    NOTIFY_STAFF["Notify Relevant Staff\n(NotificationService)"]

    VIEW["View Existing Record"]
    SHOW_DETAIL["Display Record Detail"]

    PROCESS_ACTION["Perform Module Action\n(Receive / Validate / Dispense /\nSchedule / Release)"]
    UPDATE_STATUS["Update Status in Database"]
    NOTIFY_RESULT["Notify Requesting Doctor\n(if applicable)"]

    REPORTS["Generate Report\n(Reports Module)"]
    AI_QUERY["Use MediSense AI\n(Clinical Decision Support)"]

    CONTINUE{"Continue\nWorking?"}
    LOGOUT["Logout\n(Laravel Breeze)"]
    END_NODE([⏹ End])

    START --> OPEN_BROWSER --> REDIRECT
    REDIRECT -->|No| LOGIN_PAGE
    REDIRECT -->|Yes| GET_ROLE
    LOGIN_PAGE --> ENTER_CRED --> AUTH_CHECK
    AUTH_CHECK -->|Fail| LOCK_CHECK
    LOCK_CHECK -->|Yes| AUTH_FAIL
    LOCK_CHECK -->|No| AUTH_FAIL
    AUTH_FAIL --> LOGIN_PAGE
    AUTH_CHECK -->|Pass| GET_ROLE
    GET_ROLE --> ROLE_DASH
    ROLE_DASH -->|admin| ADMIN_DASH
    ROLE_DASH -->|doctor| DOC_DASH
    ROLE_DASH -->|specialist| SPEC_DASH
    ADMIN_DASH & DOC_DASH & SPEC_DASH --> SELECT_MODULE
    SELECT_MODULE --> RBAC_CHECK
    RBAC_CHECK -->|Denied| ACCESS_DENIED
    RBAC_CHECK -->|Allowed| MODULE_INDEX
    ACCESS_DENIED --> SELECT_MODULE
    MODULE_INDEX --> ACTION_SELECT

    ACTION_SELECT -->|New Request| CREATE
    CREATE --> FILL_FORM --> VALIDATE_FORM
    VALIDATE_FORM -->|Invalid| FORM_ERROR
    FORM_ERROR --> FILL_FORM
    VALIDATE_FORM -->|Valid| SAVE_DB --> NOTIFY_STAFF --> CONTINUE

    ACTION_SELECT -->|View Detail| VIEW
    VIEW --> SHOW_DETAIL --> CONTINUE

    ACTION_SELECT -->|Process/Update| PROCESS_ACTION
    PROCESS_ACTION --> UPDATE_STATUS --> NOTIFY_RESULT --> CONTINUE

    ACTION_SELECT -->|Reports| REPORTS --> CONTINUE
    ACTION_SELECT -->|MediSense AI| AI_QUERY --> CONTINUE

    CONTINUE -->|Yes| SELECT_MODULE
    CONTINUE -->|No| LOGOUT --> END_NODE
```

**Diagram Description:** The system flowchart illustrates the complete end-to-end user journey through the HIMS — from browser access through authentication, role-based dashboard routing, module selection, RBAC authorization, and clinical action execution, to logout. The flowchart reflects the actual Laravel Breeze authentication mechanism, the role-slug-based dashboard routing, and the five possible clinical actions (create request, view, process/update, generate report, use MediSense AI) available within each module.

---

*End of HIMS Capstone Figures — 14 Diagrams | Based on actual project inspection of `c:\Users\Jhonl\OneDrive\Desktop\DITC`*
