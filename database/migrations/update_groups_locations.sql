-- Atualizar endereços e bairros dos grupos para Fortaleza
UPDATE `growth_groups` 
SET 
    location = CASE id % 20
        WHEN 0 THEN 'Rua Ana Bilhar, 1000'
        WHEN 1 THEN 'Avenida Beira Mar, 500'
        WHEN 2 THEN 'Rua Torres Câmara, 234'
        WHEN 3 THEN 'Avenida Santos Dumont, 1500'
        WHEN 4 THEN 'Rua Frederico Borges, 545'
        WHEN 5 THEN 'Avenida Padre Antônio Tomás, 850'
        WHEN 6 THEN 'Rua República do Líbano, 1020'
        WHEN 7 THEN 'Avenida Desembargador Moreira, 2500'
        WHEN 8 THEN 'Rua Monsenhor Bruno, 450'
        WHEN 9 THEN 'Avenida Dom Luís, 1200'
        WHEN 10 THEN 'Rua Costa Barros, 800'
        WHEN 11 THEN 'Avenida Aguanambi, 1800'
        WHEN 12 THEN 'Rua Padre Valdevino, 350'
        WHEN 13 THEN 'Avenida 13 de Maio, 2100'
        WHEN 14 THEN 'Rua Barão de Studart, 1500'
        WHEN 15 THEN 'Avenida Antônio Sales, 1400'
        WHEN 16 THEN 'Rua General Sampaio, 1100'
        WHEN 17 THEN 'Avenida Bezerra de Menezes, 2800'
        WHEN 18 THEN 'Rua José Vilar, 700'
        WHEN 19 THEN 'Avenida Washington Soares, 3500'
    END,
    neighborhood = CASE id % 20
        WHEN 0 THEN 'Meireles'
        WHEN 1 THEN 'Meireles'
        WHEN 2 THEN 'Aldeota'
        WHEN 3 THEN 'Aldeota'
        WHEN 4 THEN 'Varjota'
        WHEN 5 THEN 'Cocó'
        WHEN 6 THEN 'Meireles'
        WHEN 7 THEN 'Aldeota'
        WHEN 8 THEN 'Fátima'
        WHEN 9 THEN 'Aldeota'
        WHEN 10 THEN 'Centro'
        WHEN 11 THEN 'Fátima'
        WHEN 12 THEN 'Dionísio Torres'
        WHEN 13 THEN 'Benfica'
        WHEN 14 THEN 'Aldeota'
        WHEN 15 THEN 'Dionísio Torres'
        WHEN 16 THEN 'Centro'
        WHEN 17 THEN 'São Gerardo'
        WHEN 18 THEN 'Cocó'
        WHEN 19 THEN 'Edson Queiroz'
    END
WHERE status = 'active';

-- Adicionar uma mensagem de log para confirmar a atualização
SELECT CONCAT('Grupos atualizados: ', ROW_COUNT()) AS message;
