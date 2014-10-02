
/**
* currently unfinished, a cast & train section is being developed.
*/

class Skills
{
	private $dbh;
	public function __construct($database)
	{
		$this->dbh = $database;
	}
	
	 /**
	 * Fetches all skills (in the allowed skills id array)
	 * @param array $idArr
	 * @returns array of results : bool (false)
	 */
	public function fetchSkills($idArr)
	{
		$qmarks = str_repeat('?,', count($idArr) - 1) . '?';
		$query = $this->dbh->prepare("SELECT `image`,`desc`,`skillid`,`name`
			FROM `skills`
			WHERE `skillid` IN ($qmarks)
			AND `level` = 1
			ORDER BY `lvlreq` ASC");
		$query->execute($idArr);
		return ($query->rowCount() > 0) ? $query->fetchAll() : false; 
	}
	
	 /**
	 * Fetch all skills based off id, results in ASC order.
	 * @param int $skillid
	 * @returns array of results : bool (false)
	 */
	public function fetchSkillDataAsc($skillid)
	{
		$query = $this->dbh->prepare('SELECT * FROM `skills`
			WHERE `skillid` = ?
			ORDER BY `level` ASC');
		$query->execute(array($skillid));
		return ($query->rowCount() > 0) ? $query->fetchAll() : false;
	}
	
	 /**
	 * Fetch all skills based off id, results in DESC order.
	 * @param int $skillid
	 * @returns array of results : bool (false)
	 */
	public function fetchSkillDataDesc($skillid)
	{
		$query = $this->dbh->prepare('SELECT * FROM `skills`
			WHERE `skillid` = ?
			ORDER BY `level` DESC');
		$query->execute(array($skillid));
		return ($query->rowCount() > 0) ? $query->fetchAll() : false;
	}
	
	 /**
	 * Fetch the colum 'description'
	 * @param int $skillid
	 * @param int $level
	 * @returns 'desc' column : empty string
	 */
	public function skillDescData($skillid, $level)
	{
		$query = $this->dbh->prepare('SELECT `desc` FROM `skills`
			WHERE `skillid` = ?
			AND `level` = ?');
		$query->execute(array($skillid,$level));
		return ($query->rowCount() != 0) ? $desc = $query->fetchColumn() : '';
	}
	
	 /**
	 * Returns the column 'level'
	 * @param int $skillid
	 * @param int $userid
	 * @returns 'level' column : bool (false)
	 */
	public function playerSkillLevel($skillid, $userid)
	{
		$query = $this->dbh->prepare('SELECT `level` FROM `playerskills`
			WHERE `skillid` = ?
			AND `playerid` = ?');
		$query->execute(array($skillid,$userid));
		return ($query->rowCount() != 0) ? $level = $query->fetchColumn() : false;
	}
	
	public function castRecharge($userid, $skillid)
	{
		$query = $this->dbh->prepare('SELECT `recharge`
			FROM `castskills`
			WHERE `playerid` = ?
			AND `skillid` = ?
			AND `recharged` = 0
			ORDER BY `skillcastid` DESC');
		$query->execute(array($userid,$skillid));
		return ($query->rowCount() != 0) ? $recharge = $query->fetchColumn() : false;
	}
}
