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
        $this->_ci =& get_instance();

        // load the models that we need.
        // chances are that they are already loaded, but better safe than sorry.
        $this->_ci->load->model('Canned_Queries', 'queries', true);
        $this->_ci->load->model('Session', 'iliosSession', true);
    }

    /**
     * Retrieves calendar events comprised of offerings and independent learning sessions
     * for a given user in the context of a given school and given user role(s).
     *
     * The output of this method can be fed into <code>ICalExporter::toICal()</code> to produce an iCalendar feed.
     *
     * @param int $userId The user id.
     * @param int|null $schoolId An "owning" school id. If NULL is given then offerings across all schools are queried.
     * @param array $userRoles An list of user role ids providing.
     * @return array An array of assoc. arrays. Each item represents a calendar event, containing values keyed off by:
     *     'event_id'   ... A unique event id.
     *     'text'       ... Event description/summary.
     *     'start_date' ... Event start date.
     *     'end_date'   ... Event end date.
     *     'event_pid'  ... The parent event id of this event (in a series of event).
     *     'rec_type'   ... Recurrence type.
     *     'rec_length' ... The event duration in seconds.
     *     'location'   ... Event location.
     *
     * @see ICalExporter::toICal()
     */
    public function getData ($userId, $schoolId = null, array $userRoles = array())
    {
        // Specify the period of events to be included in the export.
        $timestart = strtotime("-5 days");     // last 5 days
        $timeend = strtotime("+2 months");   // next 2 months (~60 days)

        $offerings = $this->_ci->queries->getOfferingsDetailsForCalendarFeed($userId, $schoolId, $userRoles, $timestart,
            $timeend);
        $ilm_sessions = $this->_ci->queries->getSILMsDetailsForCalendarFeed($userId, $schoolId, $userRoles, $timestart,
            $timeend);

        $hostaddress = str_replace('http://', '', base_url());
        $hostaddress = str_replace('https://', '', $hostaddress);

        // strip the slash at the end of the string.
        $hostaddress = rtrim($hostaddress, '/');

        // Create an array of events for iCalendar from offerings array;
        // also combine offerings with same offering_ids.
        $events = array();
        foreach ($offerings as $offering) {
            $event = array();
            $id = $offering['offering_id'];

            $event['event_id'] = $id . '@' . $hostaddress; // UID
            $event['text'] = $offering['session_title']; // SUMMARY
            $event['utc_time'] = true;
            $event['event_pid'] = null;
            $event['rec_type'] = null;
            $event['event_length'] = null;
            $event['start_date'] = $offering['start_date'];
            $event['end_date'] = $offering['end_date'];
            if ($offering['published_as_tbd'] || $offering['course_published_as_tbd']) {
                $event['location'] = $this->_ci->languagemap->t('general.acronyms.to_be_decided');
                $event['event_details'] = $this->_ci->languagemap->t('general.acronyms.to_be_decided');
            } else {
                $event['location'] = array_key_exists('room', $offering) ? $offering['room'] : null;  // LOCATION
                $event['event_details'] = '';

                if ($offering['description']) {
                    $event['event_details'] .= $this->_unHTML($offering['description']) . "\n";
                }

                // Taught by
                if (is_array($offering['instructors'])) {
                    $event['event_details'] .= $this->_ci->languagemap->t('general.phrases.taught_by') . ' '
                        . implode(', ', $offering['instructors']) . "\n";
                }

                // This offering is a(n)
                $event['event_details'] .= $this->_ci->languagemap->t('dashboard.offering_description.offering_type')
                    . ' ' . $offering['session_type'];

                if ($offering['supplemental']) {
                    $event['event_details'] .= $this->_ci->languagemap->t('dashboard.offering_description.offering_supplemental_suffix') . "\n";
                } else {
                    $event['event_details'] .= "\n";
                }
                if ($offering['attire_required']) {
                    $event['event_details'] .= $this->_ci->languagemap->t('dashboard.offering_description.special_attire'). "\n";
                }
                if ($offering['equipment_required']) {
                    $event['event_details'] .= $this->_ci->languagemap->t('dashboard.offering_description.special_equipment'). "\n";
                }
                if (count($offering['session_objectives']) > 0) {
                    $event['event_details'] .= "\n";
                    $event['event_details'] .= $this->_ci->languagemap->t('general.terms.session') . ' ';
                    $event['event_details'] .= $this->_ci->languagemap->t('general.terms.objectives') . "\n";
                    foreach ($offering['session_objectives'] as $objective)
                        $event['event_details'] .= $this->_unHTML($objective) . "\n";
                }
                if (count($offering['session_materials']) > 0) {
                    $event['event_details'] .= "\n";
                    $event['event_details'] .= $this->_ci->languagemap->t('general.terms.session') . ' ';
                    $event['event_details'] .= $this->_ci->languagemap->t('general.phrases.learning_materials') . "\n";
                    foreach ($offering['session_materials'] as $material) {
                        $event['event_details'] .= $this->_unHTML($material['title']);
                        if ($material['required']) {
                            $event['event_details'] .= ' (' . $this->_ci->languagemap->t('general.terms.required'). ')';
                        }
                        $$event['event_details'] .= ' (' . base_url()
                            . 'ilios.php/learning_materials/getLearningMaterialWithId?learning_material_id='
                            . $material['learning_material_id']
                            . ')';
                        $event['event_details'] .= ': ' . $this->_unHTML($material['description']) . "\n";
                    }
                }
                if (count($offering['course_objectives']) > 0) {
                    $event['event_details'] .= "\n";
                    $event['event_details'] .= $this->_ci->languagemap->t('general.terms.course') . ' ';
                    $event['event_details'] .= $this->_ci->languagemap->t('general.terms.objectives') . "\n";
                    foreach ($offering['course_objectives'] as $objective) {
                        $event['event_details'] .= $this->_unHTML($objective) . "\n";
                    }
                }
                if (count($offering['course_materials']) > 0) {
                    $event['event_details'] .= "\n";
                    $event['event_details'] .= $this->_ci->languagemap->t('general.terms.course') . ' ';
                    $event['event_details'] .= $this->_ci->languagemap->t('general.phrases.learning_materials') . "\n";
                    foreach ($offering['course_materials'] as $material) {
                        $event['event_details'] .= $this->_unHTML($material['title']);
                        $event['event_details'] .= ' (' . base_url()
                            . 'ilios.php/learning_materials/getLearningMaterialWithId?learning_material_id='
                            . $material['learning_material_id']
                            . ')';
                        if ($material['required']) {
                            $event['event_details'] .= ' (' . $this->_ci->languagemap->t('general.phrases.learning_materials') . ')';
                        }
                        $event['event_details'] .= ': ' . $this->_unHTML($material['description']) . "\n";
                    }
                }
            }
            $events[$id] = $event;
        }

        foreach ($ilm_sessions as $session) {

            $event = array();
            // Add a 'ilm-' prefex to the ilm_session id in case it conflicts with offering id.
            $id = 'ilm-' . $session['ilm_session_facet_id'];

            $event['event_id'] = $id . '@' . $hostaddress; // UID

            $event['text'] = $this->_ci->languagemap->t('course_management.session.independent_learning_short') . ': ';
            $event['text'] .= $session['hours'] . ' ';
            $event['text'] .= strtolower($this->_ci->languagemap->t('general.terms.hours')) . ' ';
            $event['text'] .= strtolower($this->_ci->languagemap->t('general.phrases.due_by')) . ' ';
            $event['text'] .= strftime('%a, %b %d', strtotime($session['due_date'])) . ' - ';
            $event['text'] .= $session['course_title'].' - '.$session['session_title']; // SUMMARY
            $event['start_date'] = $session['due_date'] . ' 17:00:00';
            $event['end_date'] = $session['due_date'] . ' 17:30:00';
            $event['event_pid'] = null;
            $event['rec_type'] = null;
            $event['event_length'] = null;
            $event['location'] = null;
            if ($session['published_as_tbd'] || $session['course_published_as_tbd']) {
                $event['event_details'] = $this->_ci->languagemap->t('general.acronyms.to_be_decided');
            } else {
                $event['event_details'] = $session['event_details'];
            }
            $events[$id] = $event;

        }
        return array_values($events);
    }

    /**
     * Helper method that transform a given HTML formatted string into plain text.
     *
     * @param string $s The markup.
     * @return string The plain text.
     */
    protected function _unHTML ($s)
    {
        return str_replace("\n", ' ', trim(strip_tags($s)));
    }
}
