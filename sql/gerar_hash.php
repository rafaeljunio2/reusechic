<?php
// Gere o hash correto de admin123 para colar no SQL
echo password_hash('admin123', PASSWORD_DEFAULT) . "\n";
