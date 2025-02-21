-- Tabela para os formulários
CREATE TABLE visitor_forms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    logo_url VARCHAR(255),
    header_text TEXT,
    footer_text TEXT,
    theme_color VARCHAR(7) DEFAULT '#007bff',
    active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela para os campos do formulário
CREATE TABLE visitor_form_fields (
    id INT PRIMARY KEY AUTO_INCREMENT,
    form_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_label VARCHAR(255) NOT NULL,
    field_type ENUM('text', 'email', 'phone', 'date', 'select', 'radio', 'checkbox') NOT NULL,
    field_options TEXT, -- Para campos do tipo select, radio, checkbox
    is_required BOOLEAN DEFAULT false,
    placeholder VARCHAR(255),
    help_text TEXT,
    validation_rules TEXT,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES visitor_forms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela para as submissões dos formulários
CREATE TABLE visitor_form_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    form_id INT NOT NULL,
    visitor_id INT,
    submission_data JSON NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES visitor_forms(id),
    FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para melhor performance
CREATE INDEX idx_visitor_forms_slug ON visitor_forms(slug);
CREATE INDEX idx_visitor_form_fields_form ON visitor_form_fields(form_id);
CREATE INDEX idx_visitor_form_submissions_form ON visitor_form_submissions(form_id);
CREATE INDEX idx_visitor_form_submissions_visitor ON visitor_form_submissions(visitor_id);
