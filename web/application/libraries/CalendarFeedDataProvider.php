<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Data provider for the iCal export feature in the user dashboard and the calendar feed API.
 *
 * Architecturally, this class provides a layer of business logic/data processing that sits between controller and model.
 */
class CalendarFeedDataProvider
{
    /**
     * @var CI_Controller
     */
    protected $_ci;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        // hold on to the CI instance
        $this->_ci &= get_instance();

        // load the models that we need.
        // chances are that they are already loaded, but better safe than sorry.
        $this->_ci->load->model('Canned_Queries', 'queries', true);
        $this->_ci->load->model('Session', 'iliosSession', true);
    }

    /**
     * Retrieves calendar events comprised of offerings and independent learning sessions
     * for a given user in the context of a given school and given user role(s).
     *
     * @param int $userId The user id.
     * @param int $schoolId The school id.
     * @param array $userRoles An list of user role ids.
     * @return array A list of calendar events.
     * @todo Improve code docs, elaborate on the return value of this method. [ST 2013/12/13]
     */
    public function getData ($userId, $schoolId, array $userRoles)
    {

        // Specify the period of events to be included in the export.
        $timestart = strtotime("-5 days");     // last 5 days
        $timeend   = strtotime("+2 months");   // next 2 months (~60 days)

        $offerings = $this->_ci->queries->getOfferingsDetailsForCalendar($schoolId, $userId, $userRoles, null, false,
            $timestart, $timeend);
        $ilm_sessions = $this->_ci->queries->getSILMsForCalendar( $schoolId, $userId, $userRoles);

        $hostaddress = str_replace('http://', '', base_url());
        $hostaddress = str_replace('https://', '', $hostaddress);

        if ("/" == substr($hostaddress, strlen($hostaddress) - 1)) {
            // strip the slash at the end of the string.
            $hostaddress = substr($hostaddress, 0, strlen($hostaddress) - 1);
        }

        // Create an array of events for iCalendar from offerings array;
        // also combine offerings with same offering_ids.
        $events = array();
        foreach ($offerings as $offering) {
            $id = $offering['offering_id'];

            $offering['event_id'] = $id.'@'.$hostaddress; // UID
            $offering['text'] = $offering['session_title']; // SUMMARY
            $offering['location'] = array_key_exists('room', $offering) ? $offering['room'] : null;  // LOCATION
            $offering['utc_time'] = true;
            $offering['event_pid'] = null;
            $offering['rec_type'] = null;
            $offering['event_length'] = null;
            $details = '';

            if ($offering['description'])
                $details = unHTML($offering['description']) . "\n";

            // Taught by
            if (is_array($offering['instructors'])) {
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.phrases.taught_by')
                    . ' ' . implode(', ', $offering['instructors']) . "\n";
            }

            // This offering is a(n)
            $details .= $this->_ci->languagemap->getI18NString(
                    'dashboard.offering_description.offering_type')
                . ' ' . $offering['session_type'];

            if ($offering['supplemental']) {
                $details .=  $this->_ci->languagemap->getI18NString(
                        'dashboard.offering_description.offering_supplemental_suffix')
                    . "\n";
            } else {
                $details .= "\n";
            }
            if ($offering['attire_required']) {
                $details .= $this->_ci->languagemap->getI18NString(
                        'dashboard.offering_description.special_attire'). "\n";
            }
            if ($offering['equipment_required']) {
                $details .= $this->_ci->languagemap->getI18NString(
                        'dashboard.offering_description.special_equipment'). "\n";
            }
            if (count($offering['session_objectives']) > 0) {
                $details .= "\n";
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.terms.session') . ' ';
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.terms.objectives') . "\n";
                foreach ($offering['session_objectives'] as $objective)
                    $details .= unHTML($objective) . "\n";
            }
            if (count($offering['session_materials']) > 0) {
                $details .= "\n";
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.terms.session') . ' ';
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.phrases.learning_materials') . "\n";
                foreach ($offering['session_materials'] as $material) {
                    $details .= unHTML($material['title']);
                    if ($material['required']) {
                        $details .= ' (' . $this->_ci->languagemap->getI18NString(
                                'general.terms.required'). ')';
                    }
                    $details .= ' (' . base_url()
                        . 'ilios.php/learning_materials/getLearningMaterialWithId?learning_material_id=' . $material['learning_material_id']
                        . ')';
                    $details .= ': ' . unHTML($material['description']) . "\n";
                }
            }
            if (count($offering['course_objectives']) > 0) {
                $details .= "\n";
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.terms.course') . ' ';
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.terms.objectives') . "\n";
                foreach ($offering['course_objectives'] as $objective)
                    $details .= unHTML($objective) . "\n";
            }
            if (count($offering['course_materials']) > 0) {
                $details .= "\n";
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.terms.course') . ' ';
                $details .= $this->_ci->languagemap->getI18NString(
                        'general.phrases.learning_materials') . "\n";
                foreach ($offering['course_materials'] as $material) {
                    $details .= unHTML($material['title']);
                    $details .= ' (' . base_url()
                        . 'ilios.php/learning_materials/getLearningMaterialWithId?learning_material_id=' . $material['learning_material_id']
                        . ')';
                    if ($material['required']) {
                        $details .= ' (' . $this->_ci->languagemap->getI18NString(
                                'general.phrases.learning_materials') . ')';
                    }
                    $details .= ': ' . unHTML($material['description']) . "\n";
                }
            }

            $offering['event_details'] = $details;

            $events[$id] = $offering;
        }

        foreach ($ilm_sessions as $session) {
            $ilm_session = $this->_ci->iliosSession->getSILMBySessionId($session['session_id']);

            if (empty($ilm_session))
                continue;

            if ($timestart <= strtotime($session['due_date']) and
                $timeend >= strtotime($session['due_date'])) {

                // Add a 'ilm-' prefex to the ilm_session id in case it conflicts with offering id.
                $id = 'ilm-'.$ilm_session['ilm_session_facet'];

                $session['event_id'] = $id.'@'.$hostaddress; // UID

                $session['text'] = $this->_ci->languagemap->getI18NString('course_management.session.independent_learning_short').': ';
                $session['text'] .= $ilm_session['hours'] . ' ';
                $session['text'] .= strtolower($this->_ci->languagemap->getI18NString('general.terms.hours')).' ';
                $session['text'] .= strtolower($this->_ci->languagemap->getI18NString('general.phrases.due_by')).' ';
                $session['text'] .= strftime('%a, %b %d', strtotime($session['due_date'])).' - ';
                $session['text'] .= $session['course_title'].' - '.$session['session_title']; // SUMMARY
                $session['event_details'] = $this->_ci->iliosSession->getDescription($session['session_id']);  // DESCRIPTION
                $session['start_date'] = $session['due_date'].' 17:00:00';
                $session['end_date'] = $session['due_date'].' 17:30:00';
                $session['location'] = null;
                $session['event_pid'] = null;
                $session['rec_type'] = null;
                $session['event_length'] = null;

                $events[$id] = $session;
            }
        }
        return array_values($events);
    }
}
