
<div class="main-content">
    <h1>Leaderboard</h1>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Best Handicap</th>
                <th>Rounds Played</th>
                <th>Average Score</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($leaderboard)) : ?>
                <?php $rank = 1; ?>
                <?php foreach ($leaderboard as $row) : ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo number_format($row['best_handicap'], 2); ?></td>
                        <td><?php echo $row['rounds_played']; ?></td>
                        <td><?php echo number_format($row['average_score'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7">No data available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

