class VisitorController {
    public function index() {
        // ...existing code...

        // Definir as variáveis de estatísticas
        $totalVisitors = $this->visitorModel->getTotalVisitors();
        $contactedVisitors = $this->visitorModel->getContactedVisitors();
        $forwardedToGroup = $this->visitorModel->getForwardedToGroup();
        $groupMembers = $this->visitorModel->getGroupMembers();

        // Passar as variáveis para a view
        $data = [
            'pageTitle' => 'Visitantes',
            'visitors' => $visitors,
            'pagination' => $pagination,
            'filters' => $filters,
            'stats' => $stats,
            'groups' => $groups,
            'orderBy' => $orderBy,
            'direction' => $direction,
            'totalVisitors' => $totalVisitors,
            'contactedVisitors' => $contactedVisitors,
            'forwardedToGroup' => $forwardedToGroup,
            'groupMembers' => $groupMembers
        ];

        View::render('visitors/index', $data);
    }
}
