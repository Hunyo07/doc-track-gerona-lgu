# Database Setup Instructions

To fix the document creation error, please run the following commands in your Laravel project:

## 1. Run Database Migrations
```bash
php artisan migrate
```

## 2. Seed the Database with Required Data
```bash
php artisan db:seed --class=DocumentTypeSeeder
php artisan db:seed --class=DepartmentSeeder
```

## 3. Check Database Setup
Visit: `http://127.0.0.1:8000/api/debug/database` to verify that:
- Document types exist (PR, PO, DV, etc.)
- Departments exist
- Users exist

## 4. Test Document Creation
After running the above commands, try creating a document again. The form should now work properly.

## Common Issues and Solutions

### Issue: "document_type_id field is required"
**Solution**: Make sure DocumentTypeSeeder has been run and document types exist in the database.

### Issue: "department_id field is required"  
**Solution**: Make sure DepartmentSeeder has been run and departments exist in the database.

### Issue: "The selected document_type_id is invalid"
**Solution**: The document_type_id being sent doesn't exist in the database. Check the debug endpoint.

### Issue: "The selected department_id is invalid"
**Solution**: The department_id being sent doesn't exist in the database. Check the debug endpoint.

## Manual Database Setup (if seeders don't work)

If the seeders don't exist or don't work, you can manually insert the required data:

### Document Types
```sql
INSERT INTO document_types (name, code, description, prefix, requires_approval, is_active, created_at, updated_at) VALUES
('Purchase Request', 'PR', 'Purchase Request documents for procurement', 'PR', 1, 1, NOW(), NOW()),
('Purchase Order', 'PO', 'Purchase Order documents for approved procurement', 'PO', 1, 1, NOW(), NOW()),
('Disbursement Voucher', 'DV', 'Disbursement Voucher for payment processing', 'DV', 1, 1, NOW(), NOW()),
('Bid Document', 'BID', 'Bidding and procurement related documents', 'BID', 1, 1, NOW(), NOW()),
('Award Document', 'AWD', 'Award and contract documents', 'AWD', 1, 1, NOW(), NOW()),
('Contract', 'CON', 'Contract documents', 'CON', 1, 1, NOW(), NOW()),
('General Document', 'GEN', 'General office documents', 'DOC', 0, 1, NOW(), NOW());
```

### Departments
```sql
INSERT INTO departments (name, code, description, is_active, created_at, updated_at) VALUES
('General Services Office', 'GSO', 'General Services Office', 1, NOW(), NOW()),
('Finance Office', 'FIN', 'Finance Office', 1, NOW(), NOW()),
('Mayor Office', 'MAYOR', 'Mayor Office', 1, NOW(), NOW()),
('Human Resources', 'HR', 'Human Resources Department', 1, NOW(), NOW()),
('Engineering Office', 'ENG', 'Engineering Office', 1, NOW(), NOW());
```

## Verification Steps

1. Check that the migrations have been applied:
   ```bash
   php artisan migrate:status
   ```

2. Verify data exists:
   ```bash
   php artisan tinker
   >>> App\Models\DocumentType::count()
   >>> App\Models\Department::count()
   ```

3. Test the API endpoints:
   - GET `/api/document-types` - should return document types
   - GET `/api/departments` - should return departments
   - GET `/api/debug/database` - should show all data

After completing these steps, the document creation form should work without the 422 validation errors.