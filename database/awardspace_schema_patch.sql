-- Run this after the first schema import if reviews table already exists.
-- It makes the schema match the current application code.

ALTER TABLE reviews
    MODIFY service_id INT UNSIGNED DEFAULT NULL;

ALTER TABLE reviews
    ADD COLUMN staff_id INT UNSIGNED DEFAULT NULL AFTER service_id;

ALTER TABLE reviews
    ADD KEY reviews_staff_index (staff_id);

ALTER TABLE reviews
    ADD CONSTRAINT reviews_staff_fk FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL;
