-- Criar tabela de formulários de visitantes
CREATE TABLE IF NOT EXISTS visitor_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    logo_url VARCHAR(255),
    header_text TEXT,
    footer_text TEXT,
    theme_color VARCHAR(7) DEFAULT '#007bff',
    active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Criar tabela de campos do formulário
CREATE TABLE IF NOT EXISTS visitor_form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    field_name VARCHAR(255) NOT NULL,
    field_label VARCHAR(255) NOT NULL,
    field_type ENUM('text', 'email', 'phone', 'date', 'select', 'radio', 'checkbox', 'textarea') NOT NULL,
    field_options TEXT,
    is_required BOOLEAN DEFAULT false,
    placeholder VARCHAR(255),
    help_text TEXT,
    validation_rules TEXT,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES visitor_forms(id) ON DELETE CASCADE
);

-- Criar tabela de submissões do formulário
CREATE TABLE IF NOT EXISTS visitor_form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    visitor_id INT,
    data JSON NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES visitor_forms(id) ON DELETE CASCADE,
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE SET NULL
);
