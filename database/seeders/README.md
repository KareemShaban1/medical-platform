# Medical Platform Database Seeders

## Overview
Comprehensive database seeding system for the Medical Platform with realistic medical data including clinics, doctors, patients, appointments, medical records, prescriptions, and lab orders.

## Seeder Structure

### Execution Order
The seeders are executed in a specific order to maintain data integrity:

```
1. AdminSeeder                  → Super admin user
2. RoleAndPermissionSeeder     → Roles and permissions for all guards
3. SpecialitySeeder            → Medical specialities
4. ClinicSeeder                → 5 clinics with approvals
5. ClinicUserSeeder            → Admins + 2-3 doctors per clinic
6. WorkingHourSeeder           → Working hours + 60 days of periods
7. PatientSeeder               → 15-25 patients per clinic
8. AppointmentSeeder           → Appointments (60-90% capacity)
9. MedicalRecordSeeder         → Medical records (80% of completed)
10. PrescriptionSeeder         → Prescriptions (70% of completed)
11. LabOrderSeeder             → 5-15 lab orders per clinic
```

## Quick Start

### Reset and Seed Fresh Database

```bash
php artisan migrate:fresh --seed
```

### Seed Existing Database

```bash
php artisan db:seed
```

### Seed Specific Seeder

```bash
php artisan db:seed --class=PatientSeeder
```

## What Gets Created

### 🏥 Clinics (5)
- **Medical Excellence Clinic** - Cairo
- **City Health Center** - Alexandria
- **Prime Care Medical Center** - Giza
- **Family Wellness Clinic** - Mansoura
- **Advanced Medical Institute** - Tanta

Each clinic includes:
- Approval record
- 1 Clinic Administrator
- 2-3 Doctors with full profiles
- 15-25 Patients
- Working hours and availability
- Appointments, records, prescriptions, lab orders

### 👨‍⚕️ Doctors (10-15 total)
Specialties included:
- **Cardiologist** - Heart conditions
- **Pediatrician** - Children's health
- **Orthopedic Surgeon** - Bones and joints
- **Dermatologist** - Skin conditions
- **General Physician** - Primary care
- **Gynecologist** - Women's health

Each doctor has:
- Complete profile with bio, education, experience
- Social media links
- Profile photo (generated avatar)
- Approved status
- Working hours (various schedules)
- Assigned patients

### 🧑‍🤝‍🧑 Patients (75-125 total)
- Unique users with accounts
- Realistic names and contact info
- Assigned to 1-2 doctors per clinic
- Can visit multiple clinics
- Linked through `doctor_patient` pivot table

### 📅 Appointments
- Generated for 60 days (past and future)
- 60-90% capacity filled
- Realistic status distribution:
  - **Past:** 85% completed, 15% cancelled
  - **Today:** 40% waiting, 40% confirmed, 20% completed
  - **Future:** 80% confirmed, 20% pending
- Visit types: 50% initial, 30% follow-up, 20% consultation
- Payment tracking

### 📄 Medical Records
- Created for 80% of completed appointments
- Includes:
  - Chief complaint
  - Diagnosis
  - Treatment plan
  - Doctor notes
  - Visit type classification
  - 70% shared with patients

### 💊 Prescriptions
- Created for 70% of completed appointments
- Each prescription has 1-4 medications
- Includes:
  - Drug name
  - Dosage
  - Frequency
  - Duration
  - Special notes
- Realistic medications (Amoxicillin, Ibuprofen, etc.)

### 🧪 Lab Orders
- 5-15 orders per clinic
- Various test types:
  - Blood tests (CBC, Metabolic Panel, etc.)
  - Imaging (X-Ray, Ultrasound)
  - Specialized tests (HbA1c, Thyroid, etc.)
- Status tracking: pending, received, completed
- Lab names and cost tracking
- Result comments when completed

## Default Credentials

### Super Admin
```
Email: admin@medical.com
Password: password
```

### Clinic Administrators
```
Clinic 1: admin.clinic1@medical.com
Clinic 2: admin.clinic2@medical.com
Clinic 3: admin.clinic3@medical.com
Clinic 4: admin.clinic4@medical.com
Clinic 5: admin.clinic5@medical.com
Password: password (all)
```

### Doctors
```
john.smith.clinic[1-5]@medical.com
sarah.johnson.clinic[1-5]@medical.com
ahmed.hassan.clinic[1-5]@medical.com
emily.chen.clinic[1-5]@medical.com
michael.brown.clinic[1-5]@medical.com
fatima.alrashid.clinic[1-5]@medical.com
Password: password (all)
```

### Patients
```
Email format: [firstname].[lastname].c[clinic_id].[index]@patient.com
Example: ahmed.hassan.c1.0@patient.com
Password: password (all)
```

## Features

### 🎯 Realistic Data
- Authentic medical terminology
- Proper relationships between entities
- Realistic distributions and probabilities
- Time-based status logic

### 🔗 Complete Relationships
- Patients ↔ Doctors (via pivot with clinic context)
- Appointments → Medical Records
- Appointments → Prescriptions
- Prescriptions → Items (medications)
- All entities properly scoped to clinics

### 📊 Statistical Distribution
- **Appointments:** Realistic booking patterns
- **Medical Records:** 80% completion rate
- **Prescriptions:** 70% of completed visits
- **Lab Orders:** Varied status distribution
- **Patient Assignment:** 1-2 doctors per patient

### 🌍 Multi-Clinic Support
- Each clinic is independent
- Same patient can visit multiple clinics
- Proper data isolation
- Realistic clinic sizes

## Customization

### Adjust Patient Count
Edit `PatientSeeder.php`:
```php
$patientsCount = rand(15, 25); // Change these numbers
```

### Adjust Appointment Capacity
Edit `AppointmentSeeder.php`:
```php
$appointmentsToCreate = rand(
    (int)ceil($period->capacity * 0.6),  // Min 60%
    (int)ceil($period->capacity * 0.9)   // Max 90%
);
```

### Add More Clinics
Edit `ClinicSeeder.php` and add to the `$clinics` array.

### Modify Doctor Specialties
Edit `ClinicUserSeeder.php` and update the `$doctorData` array.

## Troubleshooting

### "No clinics found!"
Run seeders in order. Clinics must be created before clinic users.

### "No daily periods found!"
Working hours must be seeded before appointments.

### Foreign key constraint fails
Ensure migrations are run before seeding:
```bash
php artisan migrate:fresh --seed
```

### Slow seeding
Disable query logging during seeding for better performance.

## Statistics Example

After running `php artisan db:seed`, you'll see:

```
📊 FINAL STATISTICS:
───────────────────────────────────────────────────────────────
   Clinics             : 5
   Doctors             : 12
   Patients            : 95
   Appointments        : 847
   Medical Records     : 542
   Prescriptions       : 472
   Lab Orders          : 58

⏱️  Execution Time: 45.32 seconds
```

## Best Practices

1. **Always seed fresh:** Use `migrate:fresh --seed` for development
2. **Check relationships:** Verify pivot tables are populated
3. **Review logs:** Check seeder output for warnings
4. **Test queries:** Verify data integrity after seeding
5. **Backup production:** Never run seeders on production!

## Data Integrity

All seeders maintain:
- ✅ Foreign key constraints
- ✅ Unique constraints (emails, phones per clinic)
- ✅ Status consistency
- ✅ Timestamp accuracy
- ✅ Role assignments
- ✅ Proper clinic scoping

## Need Help?

- Check seeder output for detailed information
- Review `DatabaseSeeder.php` for execution flow
- Inspect individual seeders for data structure
- Verify migrations are up to date

---

**Happy Seeding! 🌱**


